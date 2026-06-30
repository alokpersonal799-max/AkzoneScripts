@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 pb-16 sm:px-6 lg:px-8">
    <div class="card p-8 text-center">
        <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-500">
            <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </span>
        <h1 class="mt-5 font-display text-2xl font-extrabold text-ink-900">
            {{ $order->isCompleted() ? 'Payment successful!' : 'Order received' }}
        </h1>
        <p class="mt-2 text-slate-500">Order <span class="font-mono font-semibold text-brand-600">{{ $order->order_number }}</span> · {{ $order->created_at->format('M j, Y g:i A') }}</p>
    </div>

    <div class="card mt-6 p-6">
        <h2 class="font-display text-lg font-bold text-ink-900">Your downloads</h2>
        <div class="mt-4 divide-y divide-slate-100">
            @foreach ($order->items as $item)
                <div class="flex items-center justify-between gap-4 py-4">
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-ink-900">{{ $item->product_title }}</p>
                        <p class="text-sm text-slate-500">{{ base_symbol() }}{{ number_format($item->price, 2) }}</p>
                    </div>
                    @if ($order->isCompleted() && $item->product && $item->product->has_downloadable)
                        <a href="{{ route('download', $item) }}" class="btn-primary btn-sm flex-shrink-0" {{ $item->product->is_external_file ? 'target=_blank rel=noopener' : '' }}>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            {{ $item->product->is_external_file ? 'Get link' : 'Download' }}
                        </a>
                    @else
                        <span class="flex-shrink-0 text-sm text-slate-400">Pending</span>
                    @endif
                </div>
            @endforeach
        </div>

        <dl class="mt-4 space-y-2 border-t border-slate-100 pt-4 text-sm">
            <div class="flex justify-between"><dt class="text-slate-500">Total paid</dt><dd class="font-bold text-ink-900">{{ base_symbol() }}{{ number_format($order->total, 2) }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Payment method</dt><dd class="capitalize text-ink-900">{{ $order->payment_method }}</dd></div>
        </dl>
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('dashboard.purchases') }}" class="btn-primary btn-md">View all purchases</a>
        <a href="{{ route('products.index') }}" class="btn-ghost btn-md">Continue shopping</a>
    </div>
</div>
@endsection
