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
        $data = $request->validate(['message' => ['required', 'string', 'max:5000']]);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'is_admin' => true,
            'message' => $data['message'],
        ]);

        $ticket->update(['status' => 'answered', 'last_reply_at' => now()]);

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
