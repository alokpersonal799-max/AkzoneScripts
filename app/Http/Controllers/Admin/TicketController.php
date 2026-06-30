<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = Ticket::query()
            ->with('user')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->latest('last_reply_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.tickets.index', [
            'tickets' => $tickets,
            'filters' => $request->only('status'),
        ]);
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['messages.user', 'user']);

        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,gif,webp,pdf,zip,rar,doc,docx,txt'],
        ]);

        $payload = [
            'user_id' => $request->user()->id,
            'is_admin' => true,
            'message' => $data['message'],
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $payload['attachment_path'] = $file->store('ticket-attachments', 'local');
            $payload['attachment_name'] = $file->getClientOriginalName();
        }

        $ticket->messages()->create($payload);

        $ticket->update(['status' => 'answered', 'last_reply_at' => now()]);

        // Notify the customer of the reply.
        if ($ticket->user) {
            \Illuminate\Support\Facades\Mail::to($ticket->user->email)
                ->send(new \App\Mail\TicketReplyMail($ticket));
        }

        return back()->with('success', 'Reply sent to the customer.');
    }

    public function close(Ticket $ticket): RedirectResponse
    {
        $ticket->update(['status' => 'closed']);

        return back()->with('success', 'Ticket closed.');
    }

    public function reopen(Ticket $ticket): RedirectResponse
    {
        $ticket->update(['status' => 'answered']);

        return back()->with('success', 'Ticket reopened.');
    }
}
