@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 pb-16 sm:px-6 lg:px-8">
    <h1 class="font-display text-3xl font-bold text-white">Your cart</h1>

    @if ($items->isEmpty())
        <div class="mt-8">
            <x-empty-state title="Your cart is empty" message="Browse the marketplace and add some products to get started.">
                <x-slot:action>
                    <a href="{{ route('products.index') }}" class="rounded-lg bg-brand-400 px-5 py-2.5 text-sm font-semibold text-ink-900 hover:bg-brand-300">Browse marketplace</a>
                </x-slot:action>
            </x-empty-state>
        </div>
    @else
        <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_360px]">
            {{-- Items --}}
            <div class="space-y-4">
                @foreach ($items as $item)
                    <div class="flex items-center gap-4 rounded-2xl border border-white/5 bg-ink-800 p-4">
                        <img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}" class="h-20 w-28 flex-shrink-0 rounded-lg object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs text-brand-300">{{ $item->category?->name }}</p>
                            <h3 class="truncate font-semibold text-white">
                                <a href="{{ route('products.show', $item) }}" class="hover:text-brand-300">{{ $item->title }}</a>
                            </h3>
                            <p class="mt-1 text-sm text-slate-400">v{{ $item->version }} · {{ $item->formatted_file_size }}</p>
                        </div>
                        <div class="text-right">
                            <x-price :amount="$item->current_price" class="font-display text-lg font-bold text-white" />
                            <form method="POST" action="{{ route('cart.remove', $item) }}" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-rose-400 hover:text-rose-300">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach

                <form method="POST" action="{{ route('cart.clear') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm font-medium text-slate-400 hover:text-rose-300">Clear cart</button>
                </form>
            </div>

            {{-- Summary --}}
            <div class="lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-2xl border border-white/5 bg-ink-800 p-6">
                    <h2 class="font-display text-lg font-bold text-white">Order summary</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-400">Items ({{ $items->count() }})</dt><dd class="text-white">{{ config('marketplace.currency_symbol') }}{{ number_format($subtotal, 2) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">Tax</dt><dd class="text-white">{{ config('marketplace.currency_symbol') }}0.00</dd></div>
                        <div class="flex justify-between border-t border-white/5 pt-3 text-base font-bold"><dt class="text-white">Total</dt><dd class="text-brand-300">{{ config('marketplace.currency_symbol') }}{{ number_format($subtotal, 2) }}</dd></div>
                    </dl>

                    @auth
                        <a href="{{ route('checkout.index') }}" class="mt-6 block w-full rounded-xl bg-brand-400 px-4 py-3 text-center font-semibold text-ink-900 transition hover:bg-brand-300">
                            Proceed to checkout
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="mt-6 block w-full rounded-xl bg-brand-400 px-4 py-3 text-center font-semibold text-ink-900 transition hover:bg-brand-300">
                            Sign in to checkout
                        </a>
                    @endauth
                    <a href="{{ route('products.index') }}" class="mt-3 block text-center text-sm text-slate-400 hover:text-brand-300">Continue shopping</a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
