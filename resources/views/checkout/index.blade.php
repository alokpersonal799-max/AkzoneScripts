@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 pb-16 sm:px-6 lg:px-8">
    <h1 class="font-display text-3xl font-bold text-white">Checkout</h1>
    <p class="mt-2 text-slate-400">Complete your purchase to unlock instant downloads.</p>

    <form method="POST" action="{{ route('checkout.store') }}" class="mt-8 grid gap-8 lg:grid-cols-[1fr_360px]">
        @csrf

        {{-- Billing & payment --}}
        <div class="space-y-6">
            <div class="rounded-2xl border border-white/5 bg-ink-800 p-6">
                <h2 class="font-display text-lg font-bold text-white">Billing details</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="billing_name" class="block text-sm font-medium text-slate-300">Full name</label>
                        <input id="billing_name" name="billing_name" type="text" value="{{ old('billing_name', auth()->user()->name) }}" required
                               class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                    </div>
                    <div>
                        <label for="billing_email" class="block text-sm font-medium text-slate-300">Email</label>
                        <input id="billing_email" name="billing_email" type="email" value="{{ old('billing_email', auth()->user()->email) }}" required
                               class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-white/5 bg-ink-800 p-6" x-data="{ method: 'manual' }">
                <h2 class="font-display text-lg font-bold text-white">Payment method</h2>
                <p class="mt-1 text-sm text-slate-400">This demo runs in manual mode. Connect Stripe or PayPal keys to accept live payments.</p>
                <div class="mt-4 space-y-3">
                    @foreach (['manual' => 'Manual / Test payment', 'stripe' => 'Credit card (Stripe)', 'paypal' => 'PayPal'] as $value => $label)
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border px-4 py-3 transition"
                               :class="method === '{{ $value }}' ? 'border-brand-400/50 bg-brand-400/10' : 'border-white/10 hover:bg-white/5'">
                            <input type="radio" name="payment_method" value="{{ $value }}" x-model="method" {{ $value === 'manual' ? 'checked' : '' }}
                                   class="border-white/20 bg-ink-900 text-brand-500 focus:ring-brand-400/30">
                            <span class="text-sm font-medium text-white">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Summary --}}
        <div class="lg:sticky lg:top-24 lg:self-start">
            <div class="rounded-2xl border border-white/5 bg-ink-800 p-6">
                <h2 class="font-display text-lg font-bold text-white">Your order</h2>
                <ul class="mt-4 space-y-3">
                    @foreach ($items as $item)
                        <li class="flex items-center justify-between gap-3 text-sm">
                            <span class="truncate text-slate-300">{{ $item->title }}</span>
                            <x-price :amount="$item->current_price" class="flex-shrink-0 font-medium text-white" />
                        </li>
                    @endforeach
                </ul>
                <dl class="mt-4 space-y-3 border-t border-white/5 pt-4 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-400">Subtotal</dt><dd class="text-white">{{ config('marketplace.currency_symbol') }}{{ number_format($subtotal, 2) }}</dd></div>
                    <div class="flex justify-between border-t border-white/5 pt-3 text-base font-bold"><dt class="text-white">Total</dt><dd class="text-brand-300">{{ config('marketplace.currency_symbol') }}{{ number_format($subtotal, 2) }}</dd></div>
                </dl>
                <button type="submit" class="mt-6 w-full rounded-xl bg-brand-400 px-4 py-3 font-semibold text-ink-900 transition hover:bg-brand-300">
                    Complete purchase
                </button>
                <p class="mt-3 text-center text-xs text-slate-500">By completing this purchase you agree to our terms of service.</p>
            </div>
        </div>
    </form>
</div>
@endsection
