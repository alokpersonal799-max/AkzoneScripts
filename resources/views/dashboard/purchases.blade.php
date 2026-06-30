@extends('layouts.dashboard')

@section('dashboard')
    <div class="mb-8">
        <h1 class="font-display text-2xl font-extrabold text-ink-900">My Purchases</h1>
        <p class="mt-1 text-slate-500">Download your products anytime. You have lifetime access.</p>
    </div>

    @if ($orderItems->isEmpty())
        <x-empty-state title="No purchases yet" message="Browse the marketplace to find scripts, code and design assets.">
            <x-slot:action>
                <a href="{{ route('products.index') }}" class="btn-primary btn-md">Explore products</a>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="space-y-4">
            @foreach ($orderItems as $item)
                <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-soft sm:flex-row sm:items-center">
                    <img src="{{ $item->product?->thumbnail_url ?? 'https://placehold.co/600x400/eef2ff/2563eb?text=Akzone' }}"
                         alt="{{ $item->product_title }}" class="h-24 w-full flex-shrink-0 rounded-xl object-cover sm:w-36">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-brand-600">{{ $item->product?->category?->name ?? 'Product' }}</p>
                        <h3 class="font-bold text-ink-900">{{ $item->product_title }}</h3>
                        <p class="mt-1 text-xs text-slate-400">
                            Order <span class="font-mono">{{ $item->order->order_number }}</span> ·
                            {{ $item->created_at->format('M j, Y') }} ·
                            {{ $item->download_count }} {{ Str::plural('download', $item->download_count) }}
                        </p>
                    </div>
                    <div class="flex flex-shrink-0 items-center gap-2">
                        @if ($item->product)
                            <a href="{{ route('products.show', $item->product) }}" class="btn-ghost btn-sm">View</a>
                        @endif
                        @if ($item->order->isCompleted() && $item->product && $item->product->file_path)
                            <a href="{{ route('download', $item) }}" class="btn-primary btn-sm">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                Download
                            </a>
                        @else
                            <span class="rounded-xl bg-slate-100 px-4 py-2 text-sm text-slate-400">Unavailable</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">{{ $orderItems->links() }}</div>
    @endif
@endsection
