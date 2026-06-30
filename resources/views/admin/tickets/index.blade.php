@extends('layouts.admin')

@section('page-title', 'Support Tickets')

@section('admin')
    <form method="GET" action="{{ route('admin.tickets.index') }}" class="mb-6 flex flex-wrap items-center gap-2">
        <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-brand-500 focus:outline-none">
            <option value="">All statuses</option>
            @foreach (['open' => 'Open', 'customer-reply' => 'Customer replied', 'answered' => 'Answered', 'closed' => 'Closed'] as $val => $label)
                <option value="{{ $val }}" {{ ($filters['status'] ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Ticket</th>
                        <th class="px-5 py-3 font-semibold">Customer</th>
                        <th class="px-5 py-3 font-semibold">Priority</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold">Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($tickets as $ticket)
                        <tr class="cursor-pointer transition hover:bg-slate-50" onclick="window.location='{{ route('admin.tickets.show', $ticket) }}'">
                            <td class="px-5 py-3">
                                <p class="font-semibold text-ink-900">{{ Str::limit($ticket->subject, 40) }}</p>
                                <p class="font-mono text-xs text-slate-400">{{ $ticket->reference }}</p>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $ticket->user?->name ?? '—' }}</td>
                            <td class="px-5 py-3"><span class="capitalize text-slate-600">{{ $ticket->priority }}</span></td>
                            <td class="px-5 py-3"><x-status-badge :status="$ticket->status === 'closed' ? 'archived' : ($ticket->status === 'answered' ? 'completed' : 'pending')" /></td>
                            <td class="px-5 py-3 text-slate-500">{{ ($ticket->last_reply_at ?? $ticket->created_at)->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">No tickets found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $tickets->links() }}</div>
@endsection
