@extends('layouts.dashboard')

@section('dashboard')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="font-display text-2xl font-extrabold text-ink-900">Support tickets</h1>
            <p class="mt-1 text-slate-500">Get help from our team — we usually reply within 24 hours.</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="btn-primary btn-md">New ticket</a>
    </div>

    @if ($tickets->isEmpty())
        <x-empty-state title="No tickets yet" message="Open a ticket and our team will help you out."
            icon="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z">
            <x-slot:action>
                <a href="{{ route('tickets.create') }}" class="btn-primary btn-md">Open a ticket</a>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="card overflow-hidden">
            <div class="divide-y divide-slate-100">
                @foreach ($tickets as $ticket)
                    <a href="{{ route('tickets.show', $ticket) }}" class="flex items-center justify-between p-5 transition hover:bg-slate-50">
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-ink-900">{{ $ticket->subject }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $ticket->reference }} · updated {{ ($ticket->last_reply_at ?? $ticket->created_at)->diffForHumans() }}</p>
                        </div>
                        <x-status-badge :status="$ticket->status === 'customer-reply' ? 'pending' : ($ticket->status === 'closed' ? 'archived' : ($ticket->status === 'answered' ? 'completed' : 'pending'))" />
                    </a>
                @endforeach
            </div>
        </div>
        <div class="mt-6">{{ $tickets->links() }}</div>
    @endif

    @include('partials.ads', ['page' => 'support'])
@endsection
