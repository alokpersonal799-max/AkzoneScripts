@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 pb-16 sm:px-6 lg:px-8">
    <h1 class="font-display text-3xl font-extrabold tracking-tight text-ink-900">Your cart</h1>

    @if ($items->isEmpty())
        <div class="mt-8">
            <x-empty-state title="Your cart is empty" message="Browse the marketplace and add some products to get started.">
                <x-slot:action>
                    <a href="{{ route('products.index') }}" class="btn-primary btn-md">Browse marketplace</a>
                </x-slot:action>
            </x-empty-state>
        </div>
    @else
        <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_360px]">
            <div class="space-y-4">
                @foreach ($items as $item)
                    <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-soft">
                        <img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}" class="h-20 w-28 flex-shrink-0 rounded-xl object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-brand-600">{{ $item->category?->name }}</p>
                            <h3 class="truncate font-bold text-ink-900">
                                <a href="{{ route('products.show', $item) }}" class="hover:text-brand-600">{{ $item->title }}</a>
                            </h3>
                            <p class="mt-1 text-sm text-slate-500">v{{ $item->version }} · {{ $item->formatted_file_size }}</p>
                        </div>
                        <div class="text-right">
                            <x-price :amount="$item->current_price" class="font-display text-lg font-extrabold text-ink-900" />
                            <form method="POST" action="{{ route('cart.remove', $item) }}" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-rose-500 hover:text-rose-600">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach

                <form method="POST" action="{{ route('cart.clear') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm font-medium text-slate-400 hover:text-rose-500">Clear cart</button>
                </form>
            </div>

            <div class="lg:sticky lg:top-24 lg:self-start">
                <div class="card p-6">
                    <h2 class="font-display text-lg font-bold text-ink-900">Order summary</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">Items ({{ $items->count() }})</dt><dd class="text-ink-900">{{ config('marketplace.currency_symbol') }}{{ number_format($subtotal, 2) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Tax</dt><dd class="text-ink-900">{{ config('marketplace.currency_symbol') }}0.00</dd></div>
                        <div class="flex justify-between border-t border-slate-100 pt-3 text-base font-bold"><dt class="text-ink-900">Total</dt><dd class="text-brand-600">{{ config('marketplace.currency_symbol') }}{{ number_format($subtotal, 2) }}</dd></div>
                    </dl>

                    @auth
                        <a href="{{ route('checkout.index') }}" class="btn-primary btn-lg mt-6 w-full">Proceed to checkout</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary btn-lg mt-6 w-full">Sign in to checkout</a>
                    @endauth
                    <a href="{{ route('products.index') }}" class="mt-3 block text-center text-sm text-slate-500 hover:text-brand-600">Continue shopping</a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
