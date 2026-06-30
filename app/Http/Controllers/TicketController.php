<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
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
        ]);

        $ticket = $request->user()->tickets()->create([
            'subject' => $data['subject'],
            'priority' => $data['priority'],
            'status' => 'open',
            'last_reply_at' => now(),
        ]);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'is_admin' => false,
            'message' => $data['message'],
        ]);

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

        $data = $request->validate(['message' => ['required', 'string', 'max:5000']]);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'is_admin' => false,
            'message' => $data['message'],
        ]);

        $ticket->update(['status' => 'customer-reply', 'last_reply_at' => now()]);

        return back()->with('success', 'Reply sent.');
    }
}
