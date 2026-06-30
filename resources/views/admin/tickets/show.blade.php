@extends('layouts.admin')

@section('page-title', 'Ticket')

@section('admin')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('admin.tickets.index') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back to tickets</a>
            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="font-display text-2xl font-extrabold text-ink-900">{{ $ticket->subject }}</h1>
                <x-status-badge :status="$ticket->status === 'closed' ? 'archived' : ($ticket->status === 'answered' ? 'completed' : 'pending')" />
            </div>
            <p class="mt-1 text-xs text-slate-400">{{ $ticket->reference }} · {{ $ticket->user?->name }} ({{ $ticket->user?->email }})</p>
        </div>
        <div class="flex gap-2">
            @if ($ticket->isClosed())
                <form method="POST" action="{{ route('admin.tickets.reopen', $ticket) }}">@csrf @method('PATCH')<button class="btn-ghost btn-sm">Reopen</button></form>
            @else
                <form method="POST" action="{{ route('admin.tickets.close', $ticket) }}" onsubmit="return confirm('Close this ticket?');">@csrf @method('PATCH')<button class="btn-ghost btn-sm">Close ticket</button></form>
            @endif
        </div>
    </div>

    <div class="space-y-4">
        @foreach ($ticket->messages as $message)
            <div class="flex gap-3 {{ $message->is_admin ? 'flex-row-reverse' : '' }}">
                <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full text-sm font-bold text-white {{ $message->is_admin ? 'bg-gradient-to-br from-brand-500 to-indigo-500' : 'bg-slate-400' }}">
                    {{ $message->is_admin ? 'A' : strtoupper(substr($message->user->name ?? 'U', 0, 1)) }}
                </span>
                <div class="max-w-[80%] rounded-2xl border border-slate-200 bg-white p-4 {{ $message->is_admin ? 'bg-brand-50/50' : '' }}">
                    <p class="text-xs font-semibold text-ink-900">{{ $message->is_admin ? 'You (support)' : ($message->user->name ?? 'Customer') }} <span class="ml-1 font-normal text-slate-400">{{ $message->created_at->diffForHumans() }}</span></p>
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-600">{{ $message->message }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card mt-6 p-6">
        @if ($ticket->isClosed())
            <p class="text-center text-sm text-slate-500">This ticket is closed. Reopen it to reply.</p>
        @else
            <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}" class="space-y-3">
                @csrf
                <textarea name="message" rows="4" required class="input" placeholder="Reply to the customer..."></textarea>
                <button type="submit" class="btn-primary btn-md">Send reply</button>
            </form>
        @endif
    </div>
@endsection
