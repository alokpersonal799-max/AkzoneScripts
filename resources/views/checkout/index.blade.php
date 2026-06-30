@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 pb-16 sm:px-6 lg:px-8">
    <h1 class="font-display text-3xl font-extrabold tracking-tight text-ink-900">Checkout</h1>
    <p class="mt-2 text-slate-500">Complete your purchase to unlock instant downloads.</p>

    <form method="POST" action="{{ route('checkout.store') }}" class="mt-8 grid gap-8 lg:grid-cols-[1fr_360px]">
        @csrf

        <div class="space-y-6">
            <div class="card p-6">
                <h2 class="font-display text-lg font-bold text-ink-900">Billing details</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="billing_name" class="label">Full name</label>
                        <input id="billing_name" name="billing_name" type="text" value="{{ old('billing_name', auth()->user()->name) }}" required class="input">
                    </div>
                    <div>
                        <label for="billing_email" class="label">Email</label>
                        <input id="billing_email" name="billing_email" type="email" value="{{ old('billing_email', auth()->user()->email) }}" required class="input">
                    </div>
                </div>
            </div>

            <div class="card p-6" x-data="{ method: 'manual' }">
                <h2 class="font-display text-lg font-bold text-ink-900">Payment method</h2>
                <p class="mt-1 text-sm text-slate-500">This demo runs in manual mode. Connect Stripe or PayPal keys to accept live payments.</p>
                <div class="mt-4 space-y-3">
                    @foreach (['manual' => 'Manual / Test payment', 'stripe' => 'Credit card (Stripe)', 'paypal' => 'PayPal'] as $value => $label)
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border px-4 py-3 transition"
                               :class="method === '{{ $value }}' ? 'border-brand-300 bg-brand-50' : 'border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="payment_method" value="{{ $value }}" x-model="method" {{ $value === 'manual' ? 'checked' : '' }}
                                   class="border-slate-300 text-brand-600 focus:ring-brand-500/30">
                            <span class="text-sm font-semibold text-ink-900">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="lg:sticky lg:top-24 lg:self-start">
            <div class="card p-6">
                <h2 class="font-display text-lg font-bold text-ink-900">Your order</h2>
                <ul class="mt-4 space-y-3">
                    @foreach ($items as $item)
                        <li class="flex items-center justify-between gap-3 text-sm">
                            <span class="truncate text-slate-600">{{ $item->title }}</span>
                            <x-price :amount="$item->current_price" class="flex-shrink-0 font-semibold text-ink-900" />
                        </li>
                    @endforeach
                </ul>
                <dl class="mt-4 space-y-3 border-t border-slate-100 pt-4 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd class="text-ink-900">{{ config('marketplace.currency_symbol') }}{{ number_format($subtotal, 2) }}</dd></div>
                    <div class="flex justify-between border-t border-slate-100 pt-3 text-base font-bold"><dt class="text-ink-900">Total</dt><dd class="text-brand-600">{{ config('marketplace.currency_symbol') }}{{ number_format($subtotal, 2) }}</dd></div>
                </dl>
                <button type="submit" class="btn-primary btn-lg mt-6 w-full">Complete purchase</button>
                <p class="mt-3 text-center text-xs text-slate-400">By completing this purchase you agree to our terms of service.</p>
            </div>
        </div>
    </form>
</div>
@endsection
