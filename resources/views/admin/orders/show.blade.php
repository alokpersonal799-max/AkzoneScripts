@extends('layouts.admin')

@section('page-title', 'Order details')

@section('admin')
    <div class="mb-6">
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back to orders</a>
        <div class="mt-2 flex flex-wrap items-center gap-3">
            <h2 class="font-display text-2xl font-extrabold text-ink-900">{{ $order->order_number }}</h2>
            <x-status-badge :status="$order->status" />
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
        {{-- Items --}}
        <div class="card overflow-hidden">
            <div class="border-b border-slate-100 p-5">
                <h3 class="font-display text-base font-bold text-ink-900">Items ({{ $order->items->count() }})</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($order->items as $item)
                    <div class="flex items-center gap-4 p-5">
                        <img src="{{ $item->product?->thumbnail_url ?? 'https://placehold.co/600x400/eef2ff/2563eb?text=Akzone' }}" alt="" class="h-12 w-16 flex-shrink-0 rounded-lg object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-ink-900">{{ $item->product_title }}</p>
                            <p class="text-xs text-slate-400">{{ $item->download_count }} downloads</p>
                        </div>
                        <p class="font-semibold text-ink-900">{{ config('marketplace.currency_symbol') }}{{ number_format($item->price, 2) }}</p>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-slate-100 p-5">
                <div class="flex justify-between text-sm"><span class="text-slate-500">Subtotal</span><span class="text-ink-900">{{ config('marketplace.currency_symbol') }}{{ number_format($order->subtotal, 2) }}</span></div>
                <div class="mt-2 flex justify-between text-base font-bold"><span class="text-ink-900">Total</span><span class="text-brand-600">{{ config('marketplace.currency_symbol') }}{{ number_format($order->total, 2) }}</span></div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="font-display text-base font-bold text-ink-900">Customer</h3>
                <dl class="mt-4 space-y-2 text-sm">
                    <div><dt class="text-slate-400">Name</dt><dd class="text-ink-900">{{ $order->user?->name ?? $order->billing_name }}</dd></div>
                    <div><dt class="text-slate-400">Email</dt><dd class="text-ink-900">{{ $order->billing_email }}</dd></div>
                    <div><dt class="text-slate-400">Payment</dt><dd class="capitalize text-ink-900">{{ $order->payment_method }}</dd></div>
                    <div><dt class="text-slate-400">Transaction</dt><dd class="font-mono text-xs text-ink-900">{{ $order->transaction_id ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Placed</dt><dd class="text-ink-900">{{ $order->created_at->format('M j, Y g:i A') }}</dd></div>
                </dl>
            </div>

            <div class="card p-6">
                <h3 class="font-display text-base font-bold text-ink-900">Update status</h3>
                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="mt-4 flex gap-2">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="input flex-1">
                        @foreach (['pending', 'completed', 'failed', 'refunded'] as $st)
                            <option value="{{ $st }}" {{ $order->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary btn-sm">Save</button>
                </form>
            </div>
        </div>
    </div>
@endsection
