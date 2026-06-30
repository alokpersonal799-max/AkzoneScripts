@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 pb-16 sm:px-6 lg:px-8">
    <h1 class="font-display text-3xl font-extrabold tracking-tight text-ink-900">Checkout</h1>
    <p class="mt-2 text-slate-500">Complete your purchase to unlock instant downloads.</p>

    <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_360px]">
        {{-- Main checkout form (left) --}}
        <form id="checkoutForm" method="POST" action="{{ route('checkout.store') }}" class="space-y-6">
            @csrf

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
        </form>

        {{-- Summary (right) — sits outside the form; the button submits via form attribute --}}
        <div class="lg:sticky lg:top-24 lg:self-start">
            <div class="card p-6">
                <h2 class="font-display text-lg font-bold text-ink-900">Your order</h2>
                <ul class="mt-4 space-y-3">
                    @forelse ($items as $item)
                        <li class="flex items-center justify-between gap-3 text-sm">
                            <span class="truncate text-slate-600">{{ $item->title }}</span>
                            <x-price :amount="$item->current_price" class="flex-shrink-0 font-semibold text-ink-900" />
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">No items available for direct checkout.</li>
                    @endforelse
                </ul>

                @if ($contactOnly->isNotEmpty())
                    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-700">
                        <p class="font-semibold">Contact to purchase</p>
                        <p class="mt-1">These items aren't available for direct checkout — contact us via WhatsApp/Telegram on their product page:</p>
                        <ul class="mt-1 list-inside list-disc">
                            @foreach ($contactOnly as $p)
                                <li><a href="{{ route('products.show', $p) }}" class="font-medium underline">{{ $p->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Coupon --}}
                <div class="mt-4 border-t border-slate-100 pt-4">
                    @if ($coupon)
                        <div class="flex items-center justify-between rounded-xl bg-emerald-50 px-3 py-2 text-sm">
                            <span class="font-semibold text-emerald-700">🏷️ {{ $coupon->code }} applied</span>
                            <form method="POST" action="{{ route('checkout.coupon.remove') }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Remove</button>
                            </form>
                        </div>
                    @else
                        <form method="POST" action="{{ route('checkout.coupon.apply') }}" class="flex gap-2">
                            @csrf
                            <input name="code" placeholder="Coupon code" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm uppercase focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10">
                            <button type="submit" class="btn-ghost btn-sm flex-shrink-0">Apply</button>
                        </form>
                    @endif
                </div>

                <dl class="mt-4 space-y-3 border-t border-slate-100 pt-4 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd class="text-ink-900">{{ money($subtotal) }}</dd></div>
                    @if ($discount > 0)
                        <div class="flex justify-between"><dt class="text-slate-500">Discount</dt><dd class="text-emerald-600">- {{ money($discount) }}</dd></div>
                    @endif
                    <div class="flex justify-between border-t border-slate-100 pt-3 text-base font-bold"><dt class="text-ink-900">Total</dt><dd class="text-brand-600">{{ money($total) }}</dd></div>
                </dl>

                @if ($items->isNotEmpty())
                    <button type="submit" form="checkoutForm" class="btn-primary btn-lg mt-6 w-full">Complete purchase</button>
                    <p class="mt-3 text-center text-xs text-slate-400">By completing this purchase you agree to our terms of service.</p>
                @else
                    <a href="{{ route('products.index') }}" class="btn-ghost btn-lg mt-6 w-full">Browse more products</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
