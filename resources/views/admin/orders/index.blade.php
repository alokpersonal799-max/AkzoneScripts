@extends('layouts.admin')

@section('page-title', 'Orders')

@section('admin')
    <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-6 flex flex-wrap items-center gap-2">
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Order # or customer email..."
               class="w-full max-w-xs rounded-lg border border-white/10 bg-ink-800 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
        <select name="status" onchange="this.form.submit()" class="rounded-lg border border-white/10 bg-ink-800 px-3 py-2 text-sm text-white focus:border-brand-400">
            <option value="">All status</option>
            @foreach (['pending', 'completed', 'failed', 'refunded'] as $st)
                <option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-white/5 px-4 py-2 text-sm font-medium text-white hover:bg-white/10">Filter</button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-white/5 bg-ink-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/5 text-sm">
                <thead class="bg-white/5 text-left text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-5 py-3 font-medium">Order</th>
                        <th class="px-5 py-3 font-medium">Customer</th>
                        <th class="px-5 py-3 font-medium">Items</th>
                        <th class="px-5 py-3 font-medium">Total</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($orders as $order)
                        <tr class="cursor-pointer transition hover:bg-white/5" onclick="window.location='{{ route('admin.orders.show', $order) }}'">
                            <td class="px-5 py-3 font-mono text-brand-300">{{ $order->order_number }}</td>
                            <td class="px-5 py-3">
                                <p class="text-white">{{ $order->user?->name ?? 'Guest' }}</p>
                                <p class="text-xs text-slate-500">{{ $order->billing_email }}</p>
                            </td>
                            <td class="px-5 py-3 text-slate-300">{{ $order->items_count }}</td>
                            <td class="px-5 py-3 font-semibold text-white">{{ config('marketplace.currency_symbol') }}{{ number_format($order->total, 2) }}</td>
                            <td class="px-5 py-3"><x-status-badge :status="$order->status" /></td>
                            <td class="px-5 py-3 text-slate-400">{{ $order->created_at->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
@endsection
