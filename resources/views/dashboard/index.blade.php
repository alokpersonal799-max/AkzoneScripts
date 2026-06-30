@extends('layouts.dashboard')

@section('dashboard')
    <div class="mb-8">
        <h1 class="font-display text-2xl font-extrabold text-ink-900">Welcome back, {{ Str::before(auth()->user()->name, ' ') }} 👋</h1>
        <p class="mt-1 text-slate-500">Here's what's happening with your account.</p>
    </div>

    {{-- Stats --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $cards = [
                ['label' => 'Products owned', 'value' => number_format($stats['purchases']), 'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5', 'tint' => 'bg-brand-50 text-brand-600'],
                ['label' => 'Total orders', 'value' => number_format($stats['orders']), 'icon' => 'M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z', 'tint' => 'bg-indigo-50 text-indigo-600'],
                ['label' => 'Total spent', 'value' => config('marketplace.currency_symbol').number_format($stats['spent'], 2), 'icon' => 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'tint' => 'bg-emerald-50 text-emerald-600'],
                ['label' => 'Wishlist items', 'value' => number_format($stats['wishlist']), 'icon' => 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z', 'tint' => 'bg-rose-50 text-rose-600'],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="card p-5">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $card['tint'] }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}" /></svg>
                </span>
                <p class="mt-4 font-display text-2xl font-extrabold text-ink-900">{{ $card['value'] }}</p>
                <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Recent downloads --}}
    <div class="mt-8">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-lg font-bold text-ink-900">Recent downloads</h2>
            <a href="{{ route('dashboard.purchases') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">View all &rarr;</a>
        </div>
        @if ($recentDownloads->isEmpty())
            <div class="mt-4">
                <x-empty-state title="No purchases yet" message="When you buy products they'll appear here for instant download.">
                    <x-slot:action>
                        <a href="{{ route('products.index') }}" class="btn-primary btn-md">Explore products</a>
                    </x-slot:action>
                </x-empty-state>
            </div>
        @else
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($recentDownloads as $product)
                    <a href="{{ route('products.show', $product) }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-soft transition hover:-translate-y-1 hover:shadow-lift">
                        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}" class="aspect-video w-full object-cover">
                        <div class="p-4">
                            <p class="truncate text-sm font-bold text-ink-900 group-hover:text-brand-600">{{ $product->title }}</p>
                            <p class="mt-1 text-xs text-slate-400">v{{ $product->version }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Recent orders --}}
    <div class="card mt-8 overflow-hidden">
        <div class="border-b border-slate-100 p-5">
            <h2 class="font-display text-lg font-bold text-ink-900">Recent orders</h2>
        </div>
        @if ($recentOrders->isEmpty())
            <p class="p-5 text-sm text-slate-500">No orders yet.</p>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($recentOrders as $order)
                    <a href="{{ route('orders.show', $order) }}" class="flex items-center justify-between p-5 transition hover:bg-slate-50">
                        <div>
                            <p class="font-mono text-sm font-semibold text-brand-600">{{ $order->order_number }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }} · {{ $order->created_at->format('M j, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-ink-900">{{ config('marketplace.currency_symbol') }}{{ number_format($order->total, 2) }}</p>
                            <x-status-badge :status="$order->status" class="mt-1" />
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
