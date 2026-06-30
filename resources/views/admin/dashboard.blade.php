@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('admin')
    {{-- Stats --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @php
            $sym = config('marketplace.currency_symbol');
            $cards = [
                ['label' => 'Revenue', 'value' => $sym.number_format($stats['revenue'], 2), 'sub' => $stats['orders'].' completed orders', 'tint' => 'bg-emerald-50 text-emerald-600', 'icon' => 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33'],
                ['label' => 'Products', 'value' => number_format($stats['products']), 'sub' => $stats['published'].' published', 'tint' => 'bg-brand-50 text-brand-600', 'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5'],
                ['label' => 'Customers', 'value' => number_format($stats['customers']), 'sub' => number_format($stats['downloads']).' total downloads', 'tint' => 'bg-indigo-50 text-indigo-600', 'icon' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Z'],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $card['tint'] }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}" /></svg>
                    </span>
                </div>
                <p class="mt-3 font-display text-3xl font-extrabold text-ink-900">{{ $card['value'] }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ $card['sub'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Sales chart --}}
    <div class="card mt-6 p-6">
        <h2 class="font-display text-lg font-bold text-ink-900">Revenue · last 7 days</h2>
        @php $maxTotal = max($salesByDay->max('total'), 1); @endphp
        <div class="mt-6 flex items-end justify-between gap-2" style="height: 200px;">
            @foreach ($salesByDay as $day)
                <div class="flex flex-1 flex-col items-center justify-end gap-2">
                    <span class="text-xs font-semibold text-slate-500">{{ $sym }}{{ number_format($day['total'], 0) }}</span>
                    <div class="w-full rounded-t-lg bg-gradient-to-t from-brand-500 to-brand-300 transition-all hover:from-brand-600 hover:to-brand-400"
                         style="height: {{ max(($day['total'] / $maxTotal) * 150, 4) }}px;" title="{{ $sym }}{{ number_format($day['total'], 2) }}"></div>
                    <span class="text-xs text-slate-400">{{ $day['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        {{-- Recent orders --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 p-5">
                <h2 class="font-display text-lg font-bold text-ink-900">Recent orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">View all</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentOrders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between p-5 transition hover:bg-slate-50">
                        <div class="min-w-0">
                            <p class="font-mono text-sm font-semibold text-brand-600">{{ $order->order_number }}</p>
                            <p class="mt-1 truncate text-xs text-slate-400">{{ $order->user?->name ?? 'Guest' }} · {{ $order->created_at->format('M j') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-ink-900">{{ $sym }}{{ number_format($order->total, 2) }}</p>
                            <x-status-badge :status="$order->status" class="mt-1" />
                        </div>
                    </a>
                @empty
                    <p class="p-5 text-sm text-slate-500">No orders yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Top products --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 p-5">
                <h2 class="font-display text-lg font-bold text-ink-900">Top products</h2>
                <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">Manage</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($topProducts as $product)
                    <div class="flex items-center gap-3 p-5">
                        <img src="{{ $product->thumbnail_url }}" alt="" class="h-12 w-16 flex-shrink-0 rounded-lg object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-ink-900">{{ $product->title }}</p>
                            <p class="text-xs text-slate-400">{{ number_format($product->downloads) }} downloads</p>
                        </div>
                        <x-status-badge :status="$product->status" />
                    </div>
                @empty
                    <p class="p-5 text-sm text-slate-500">No products yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    @if ($lowStockOrDraft->isNotEmpty())
        <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <h2 class="font-display text-base font-bold text-amber-700">Draft products awaiting publish</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($lowStockOrDraft as $product)
                    <a href="{{ route('admin.products.edit', $product) }}" class="rounded-lg border border-amber-200 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-amber-100">{{ $product->title }}</a>
                @endforeach
            </div>
        </div>
    @endif
@endsection
