@extends('layouts.admin')

@section('page-title', 'Order details')

@section('admin')
    <div class="mb-6">
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-slate-400 hover:text-brand-300">&larr; Back to orders</a>
        <div class="mt-2 flex flex-wrap items-center gap-3">
            <h2 class="font-display text-2xl font-bold text-white">{{ $order->order_number }}</h2>
            <x-status-badge :status="$order->status" />
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
        {{-- Items --}}
        <div class="rounded-2xl border border-white/5 bg-ink-800">
            <div class="border-b border-white/5 p-5">
                <h3 class="font-display text-base font-bold text-white">Items ({{ $order->items->count() }})</h3>
            </div>
            <div class="divide-y divide-white/5">
                @foreach ($order->items as $item)
                    <div class="flex items-center gap-4 p-5">
                        <img src="{{ $item->product?->thumbnail_url ?? 'https://placehold.co/600x400/0f172a/22d3ee?text=Akzone' }}" alt="" class="h-12 w-16 flex-shrink-0 rounded-lg object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-white">{{ $item->product_title }}</p>
                            <p class="text-xs text-slate-500">{{ $item->download_count }} downloads</p>
                        </div>
                        <p class="font-semibold text-white">{{ config('marketplace.currency_symbol') }}{{ number_format($item->price, 2) }}</p>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-white/5 p-5">
                <div class="flex justify-between text-sm"><span class="text-slate-400">Subtotal</span><span class="text-white">{{ config('marketplace.currency_symbol') }}{{ number_format($order->subtotal, 2) }}</span></div>
                <div class="mt-2 flex justify-between text-base font-bold"><span class="text-white">Total</span><span class="text-brand-300">{{ config('marketplace.currency_symbol') }}{{ number_format($order->total, 2) }}</span></div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="rounded-2xl border border-white/5 bg-ink-800 p-6">
                <h3 class="font-display text-base font-bold text-white">Customer</h3>
                <dl class="mt-4 space-y-2 text-sm">
                    <div><dt class="text-slate-500">Name</dt><dd class="text-white">{{ $order->user?->name ?? $order->billing_name }}</dd></div>
                    <div><dt class="text-slate-500">Email</dt><dd class="text-white">{{ $order->billing_email }}</dd></div>
                    <div><dt class="text-slate-500">Payment</dt><dd class="capitalize text-white">{{ $order->payment_method }}</dd></div>
                    <div><dt class="text-slate-500">Transaction</dt><dd class="font-mono text-xs text-white">{{ $order->transaction_id ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Placed</dt><dd class="text-white">{{ $order->created_at->format('M j, Y g:i A') }}</dd></div>
                </dl>
            </div>

            <div class="rounded-2xl border border-white/5 bg-ink-800 p-6">
                <h3 class="font-display text-base font-bold text-white">Update status</h3>
                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="mt-4 flex gap-2">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="flex-1 rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-sm text-white focus:border-brand-400">
                        @foreach (['pending', 'completed', 'failed', 'refunded'] as $st)
                            <option value="{{ $st }}" {{ $order->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-lg bg-brand-400 px-4 py-2.5 text-sm font-semibold text-ink-900 hover:bg-brand-300">Save</button>
                </form>
            </div>
        </div>
    </div>
@endsection
