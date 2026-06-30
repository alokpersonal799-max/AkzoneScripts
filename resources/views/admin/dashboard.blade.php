@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('admin')
    {{-- Stats --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @php
            $sym = config('marketplace.currency_symbol');
            $cards = [
                ['label' => 'Revenue', 'value' => $sym.number_format($stats['revenue'], 2), 'sub' => $stats['orders'].' completed orders', 'color' => 'from-emerald-400/20 to-emerald-500/5', 'text' => 'text-emerald-300'],
                ['label' => 'Products', 'value' => number_format($stats['products']), 'sub' => $stats['published'].' published', 'color' => 'from-brand-400/20 to-brand-500/5', 'text' => 'text-brand-300'],
                ['label' => 'Customers', 'value' => number_format($stats['customers']), 'sub' => number_format($stats['downloads']).' total downloads', 'color' => 'from-indigo-400/20 to-indigo-500/5', 'text' => 'text-indigo-300'],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="rounded-2xl border border-white/5 bg-gradient-to-br {{ $card['color'] }} p-6">
                <p class="text-sm text-slate-400">{{ $card['label'] }}</p>
                <p class="mt-2 font-display text-3xl font-bold {{ $card['text'] }}">{{ $card['value'] }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $card['sub'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Sales chart --}}
    <div class="mt-6 rounded-2xl border border-white/5 bg-ink-800 p-6">
        <h2 class="font-display text-lg font-bold text-white">Revenue · last 7 days</h2>
        @php $maxTotal = max($salesByDay->max('total'), 1); @endphp
        <div class="mt-6 flex items-end justify-between gap-2" style="height: 200px;">
            @foreach ($salesByDay as $day)
                <div class="flex flex-1 flex-col items-center justify-end gap-2">
                    <span class="text-xs font-medium text-slate-400">{{ $sym }}{{ number_format($day['total'], 0) }}</span>
                    <div class="w-full rounded-t-lg bg-gradient-to-t from-brand-500 to-brand-300 transition-all"
                         style="height: {{ max(($day['total'] / $maxTotal) * 150, 4) }}px;" title="{{ $sym }}{{ number_format($day['total'], 2) }}"></div>
                    <span class="text-xs text-slate-500">{{ $day['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        {{-- Recent orders --}}
        <div class="rounded-2xl border border-white/5 bg-ink-800">
            <div class="flex items-center justify-between border-b border-white/5 p-5">
                <h2 class="font-display text-lg font-bold text-white">Recent orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-brand-300 hover:text-brand-200">View all</a>
            </div>
            <div class="divide-y divide-white/5">
                @forelse ($recentOrders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between p-5 transition hover:bg-white/5">
                        <div class="min-w-0">
                            <p class="font-mono text-sm text-brand-300">{{ $order->order_number }}</p>
                            <p class="mt-1 truncate text-xs text-slate-500">{{ $order->user?->name ?? 'Guest' }} · {{ $order->created_at->format('M j') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-white">{{ $sym }}{{ number_format($order->total, 2) }}</p>
                            <x-status-badge :status="$order->status" />
                        </div>
                    </a>
                @empty
                    <p class="p-5 text-sm text-slate-400">No orders yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Top products --}}
        <div class="rounded-2xl border border-white/5 bg-ink-800">
            <div class="flex items-center justify-between border-b border-white/5 p-5">
                <h2 class="font-display text-lg font-bold text-white">Top products</h2>
                <a href="{{ route('admin.products.index') }}" class="text-sm font-medium text-brand-300 hover:text-brand-200">Manage</a>
            </div>
            <div class="divide-y divide-white/5">
                @forelse ($topProducts as $product)
                    <div class="flex items-center gap-3 p-5">
                        <img src="{{ $product->thumbnail_url }}" alt="" class="h-12 w-16 flex-shrink-0 rounded-lg object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-white">{{ $product->title }}</p>
                            <p class="text-xs text-slate-500">{{ number_format($product->downloads) }} downloads</p>
                        </div>
                        <x-status-badge :status="$product->status" />
                    </div>
                @empty
                    <p class="p-5 text-sm text-slate-400">No products yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    @if ($lowStockOrDraft->isNotEmpty())
        <div class="mt-6 rounded-2xl border border-amber-500/20 bg-amber-500/5 p-5">
            <h2 class="font-display text-base font-bold text-amber-300">Draft products awaiting publish</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($lowStockOrDraft as $product)
                    <a href="{{ route('admin.products.edit', $product) }}" class="rounded-lg border border-white/10 bg-ink-800 px-3 py-1.5 text-sm text-slate-300 hover:bg-white/5">{{ $product->title }}</a>
                @endforeach
            </div>
        </div>
    @endif
@endsection
