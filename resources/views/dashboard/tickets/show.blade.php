@extends('layouts.dashboard')

@section('dashboard')
    <div class="mb-6">
        <a href="{{ route('tickets.index') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back to tickets</a>
        <div class="mt-2 flex flex-wrap items-center gap-3">
            <h1 class="font-display text-2xl font-extrabold text-ink-900">{{ $ticket->subject }}</h1>
            <x-status-badge :status="$ticket->status === 'closed' ? 'archived' : ($ticket->status === 'answered' ? 'completed' : 'pending')" />
        </div>
        <p class="mt-1 text-xs text-slate-400">{{ $ticket->reference }}</p>
    </div>

    {{-- Conversation --}}
    <div class="space-y-4">
        @foreach ($ticket->messages as $message)
            <div class="flex gap-3 {{ $message->is_admin ? '' : 'flex-row-reverse' }}">
                <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full text-sm font-bold text-white {{ $message->is_admin ? 'bg-indigo-500' : 'bg-gradient-to-br from-brand-500 to-indigo-500' }}">
                    {{ $message->is_admin ? 'A' : strtoupper(substr($message->user->name ?? 'U', 0, 1)) }}
                </span>
                <div class="max-w-[80%] rounded-2xl border border-slate-200 bg-white p-4 {{ $message->is_admin ? '' : 'bg-brand-50/50' }}">
                    <p class="text-xs font-semibold text-ink-900">{{ $message->is_admin ? 'Support team' : ($message->user->name ?? 'You') }} <span class="ml-1 font-normal text-slate-400">{{ $message->created_at->diffForHumans() }}</span></p>
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-600">{{ $message->message }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Reply --}}
    <div class="card mt-6 p-6">
        @if ($ticket->isClosed())
            <p class="text-center text-sm text-slate-500">This ticket has been closed by our support team.</p>
        @else
            <form method="POST" action="{{ route('tickets.reply', $ticket) }}" class="space-y-3">
                @csrf
                <textarea name="message" rows="4" required class="input" placeholder="Write a reply..."></textarea>
                <button type="submit" class="btn-primary btn-md">Send reply</button>
            </form>
        @endif
    </div>
@endsection
