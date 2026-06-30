@extends('layouts.dashboard')

@section('dashboard')
    <div class="mb-8">
        <h1 class="font-display text-2xl font-bold text-white">Welcome back, {{ Str::before(auth()->user()->name, ' ') }} 👋</h1>
        <p class="mt-1 text-slate-400">Here's what's happening with your account.</p>
    </div>

    {{-- Stats --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $cards = [
                ['label' => 'Products owned', 'value' => number_format($stats['purchases']), 'color' => 'from-brand-400/20 to-brand-500/5', 'text' => 'text-brand-300'],
                ['label' => 'Total orders', 'value' => number_format($stats['orders']), 'color' => 'from-indigo-400/20 to-indigo-500/5', 'text' => 'text-indigo-300'],
                ['label' => 'Total spent', 'value' => config('marketplace.currency_symbol').number_format($stats['spent'], 2), 'color' => 'from-emerald-400/20 to-emerald-500/5', 'text' => 'text-emerald-300'],
                ['label' => 'Wishlist items', 'value' => number_format($stats['wishlist']), 'color' => 'from-rose-400/20 to-rose-500/5', 'text' => 'text-rose-300'],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="rounded-2xl border border-white/5 bg-gradient-to-br {{ $card['color'] }} p-5">
                <p class="text-sm text-slate-400">{{ $card['label'] }}</p>
                <p class="mt-2 font-display text-3xl font-bold {{ $card['text'] }}">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Recent downloads --}}
    <div class="mt-8">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-lg font-bold text-white">Recent downloads</h2>
            <a href="{{ route('dashboard.purchases') }}" class="text-sm font-medium text-brand-300 hover:text-brand-200">View all &rarr;</a>
        </div>
        @if ($recentDownloads->isEmpty())
            <div class="mt-4">
                <x-empty-state title="No purchases yet" message="When you buy products they'll appear here for instant download.">
                    <x-slot:action>
                        <a href="{{ route('products.index') }}" class="rounded-lg bg-brand-400 px-5 py-2.5 text-sm font-semibold text-ink-900 hover:bg-brand-300">Explore products</a>
                    </x-slot:action>
                </x-empty-state>
            </div>
        @else
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($recentDownloads as $product)
                    <a href="{{ route('products.show', $product) }}" class="group overflow-hidden rounded-2xl border border-white/5 bg-ink-800 transition hover:border-brand-400/30">
                        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}" class="aspect-video w-full object-cover">
                        <div class="p-4">
                            <p class="truncate text-sm font-semibold text-white group-hover:text-brand-300">{{ $product->title }}</p>
                            <p class="mt-1 text-xs text-slate-500">v{{ $product->version }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Recent orders --}}
    <div class="mt-8 rounded-2xl border border-white/5 bg-ink-800">
        <div class="border-b border-white/5 p-5">
            <h2 class="font-display text-lg font-bold text-white">Recent orders</h2>
        </div>
        @if ($recentOrders->isEmpty())
            <p class="p-5 text-sm text-slate-400">No orders yet.</p>
        @else
            <div class="divide-y divide-white/5">
                @foreach ($recentOrders as $order)
                    <a href="{{ route('orders.show', $order) }}" class="flex items-center justify-between p-5 transition hover:bg-white/5">
                        <div>
                            <p class="font-mono text-sm text-brand-300">{{ $order->order_number }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }} · {{ $order->created_at->format('M j, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-white">{{ config('marketplace.currency_symbol') }}{{ number_format($order->total, 2) }}</p>
                            <x-status-badge :status="$order->status" />
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
