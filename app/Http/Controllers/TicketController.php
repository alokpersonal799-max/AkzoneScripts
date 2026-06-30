<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketController extends Controller
{
    /**
     * Validation rule for ticket attachments (image or common doc, <= 5MB).
     */
    protected array $attachmentRule = ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,gif,webp,pdf,zip,rar,doc,docx,txt'];

    public function index(Request $request): View
    {
        $tickets = $request->user()->tickets()->latest()->paginate(10);

        return view('dashboard.tickets.index', compact('tickets'));
    }

    public function create(): View
    {
        return view('dashboard.tickets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:low,normal,high'],
            'message' => ['required', 'string', 'max:5000'],
            'attachment' => $this->attachmentRule,
        ]);

        $ticket = $request->user()->tickets()->create([
            'subject' => $data['subject'],
            'priority' => $data['priority'],
            'status' => 'open',
            'last_reply_at' => now(),
        ]);

        $ticket->messages()->create(array_merge([
            'user_id' => $request->user()->id,
            'is_admin' => false,
            'message' => $data['message'],
        ], $this->storeAttachment($request)));

        \App\Models\AdminNotification::notifyAdmins('ticket', 'New support ticket', $ticket->subject, route('admin.tickets.show', $ticket));

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Your ticket has been submitted. We will reply soon.');
    }

    public function show(Request $request, Ticket $ticket): View
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);

        $ticket->load('messages.user');

        return view('dashboard.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);

        if ($ticket->isClosed()) {
            return back()->with('error', 'This ticket has been closed.');
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'attachment' => $this->attachmentRule,
        ]);

        $ticket->messages()->create(array_merge([
            'user_id' => $request->user()->id,
            'is_admin' => false,
            'message' => $data['message'],
        ], $this->storeAttachment($request)));

        $ticket->update(['status' => 'customer-reply', 'last_reply_at' => now()]);

        \App\Models\AdminNotification::notifyAdmins('ticket', 'Customer replied to ticket', $ticket->subject, route('admin.tickets.show', $ticket));

        return back()->with('success', 'Reply sent.');
    }

    /**
     * Securely stream a ticket attachment to its owner (or any admin).
     */
    public function download(Request $request, TicketMessage $message): StreamedResponse
    {
        $message->load('ticket');
        $user = $request->user();

        abort_unless($user->isAdmin() || $message->ticket->user_id === $user->id, 403);
        abort_if(! $message->attachment_path || ! Storage::disk('local')->exists($message->attachment_path), 404);

        return Storage::disk('local')->download($message->attachment_path, $message->attachment_name);
    }

    /**
     * Store an uploaded attachment on the private disk, returning columns to merge.
     *
     * @return array<string, string|null>
     */
    protected function storeAttachment(Request $request): array
    {
        if (! $request->hasFile('attachment')) {
            return [];
        }

        $file = $request->file('attachment');

        return [
            'attachment_path' => $file->store('ticket-attachments', 'local'),
            'attachment_name' => $file->getClientOriginalName(),
        ];
    }
}
