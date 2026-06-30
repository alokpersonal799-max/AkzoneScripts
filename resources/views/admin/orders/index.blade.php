@extends('layouts.admin')

@section('page-title', 'Orders')

@section('admin')
    <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-6 flex flex-wrap items-center gap-2">
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Order # or customer email..."
               class="w-full max-w-xs rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10">
        <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-brand-500 focus:outline-none">
            <option value="">All status</option>
            @foreach (['pending', 'completed', 'failed', 'refunded'] as $st)
                <option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-ghost btn-sm">Filter</button>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Order</th>
                        <th class="px-5 py-3 font-semibold">Customer</th>
                        <th class="px-5 py-3 font-semibold">Items</th>
                        <th class="px-5 py-3 font-semibold">Total</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                        <tr class="cursor-pointer transition hover:bg-slate-50" onclick="window.location='{{ route('admin.orders.show', $order) }}'">
                            <td class="px-5 py-3 font-mono font-semibold text-brand-600">{{ $order->order_number }}</td>
                            <td class="px-5 py-3">
                                <p class="text-ink-900">{{ $order->user?->name ?? 'Guest' }}</p>
                                <p class="text-xs text-slate-400">{{ $order->billing_email }}</p>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $order->items_count }}</td>
                            <td class="px-5 py-3 font-semibold text-ink-900">{{ base_symbol() }}{{ number_format($order->total, 2) }}</td>
                            <td class="px-5 py-3"><x-status-badge :status="$order->status" /></td>
                            <td class="px-5 py-3 text-slate-500">{{ $order->created_at->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
@endsection
