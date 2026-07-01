@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 pb-16 sm:px-6 lg:px-8">
    <h1 class="font-display text-3xl font-extrabold tracking-tight text-ink-900">Checkout</h1>
    <p class="mt-2 text-slate-500">Complete your purchase to unlock instant downloads.</p>

    <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_360px]">
        {{-- Main checkout form (left) --}}
        <form id="checkoutForm" method="POST" action="{{ route('checkout.store') }}" enctype="multipart/form-data" class="space-y-6"
              x-data="{ method: '{{ array_key_first($methods) ?? 'manual' }}' }">
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
                    <div class="sm:col-span-2">
                        <label class="label">Mobile number</label>
                        <div class="flex gap-2">
                            <select name="billing_phone_country" class="input w-40 flex-shrink-0">
                                @include('partials.country-codes', ['selected' => old('billing_phone_country', auth()->user()->phone_country)])
                            </select>
                            <input name="billing_phone" type="tel" value="{{ old('billing_phone', auth()->user()->phone) }}" class="input" placeholder="Phone number">
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="billing_country" class="label">Country</label>
                        <select id="billing_country" name="billing_country" class="input">
                            <option value="">Select your country…</option>
                            @foreach (config('countries') as $code => $name)
                                <option value="{{ $code }}" @selected(old('billing_country') === $code)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h2 class="font-display text-lg font-bold text-ink-900">Payment method</h2>

                @if ($total <= 0)
                    <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        🎉 Your order total is <strong>{{ money(0) }}</strong> — no payment needed. Just click <strong>Complete order</strong> for instant access.
                    </div>
                @elseif (empty($methods))
                    <p class="mt-3 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700">No payment methods are enabled. Please contact support.</p>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach ($methods as $value => $label)
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border px-4 py-3 transition"
                                   :class="method === '{{ $value }}' ? 'border-brand-300 bg-brand-50' : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="payment_method" value="{{ $value }}" x-model="method" class="border-slate-300 text-brand-600 focus:ring-brand-500/30">
                                @if (! empty($gatewayIcons[$value]))
                                    <img src="{{ $gatewayIcons[$value] }}" alt="" class="h-6 w-auto max-w-[80px] object-contain">
                                @endif
                                <span class="text-sm font-semibold text-ink-900">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    {{-- Manual payment: pick a method, see details, pay within 10 min --}}
                    @if (isset($methods['manual']))
                        @php
                            $mUpi = $manual['upi_enabled'] && $manual['upi'];
                            $mBank = $manual['bank_enabled'] && $manual['bank'];
                            $mCrypto = $manual['crypto_enabled'] && count($manual['crypto']);
                            $firstManual = $mUpi ? 'upi' : ($mBank ? 'bank' : ($mCrypto ? 'crypto' : ''));
                        @endphp
                        <div x-show="method === 'manual'" x-cloak class="mt-5"
                             x-data="{ sub: '', started: false, secs: 600, timer: null,
                                       choose(m){ this.sub = m; if (! this.started) { this.started = true; this.start(); } },
                                       start(){ this.secs = 600; clearInterval(this.timer); this.timer = setInterval(() => { if (this.secs > 0) this.secs--; else clearInterval(this.timer); }, 1000); },
                                       get mm(){ return String(Math.floor(this.secs/60)).padStart(2,'0'); },
                                       get ss(){ return String(this.secs%60).padStart(2,'0'); },
                                       get expired(){ return this.secs <= 0; } }">

                            @if ($firstManual === '')
                                <p class="rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-700">No manual payment methods are configured yet. Please contact support.</p>
                            @else
                                @if ($manual['instructions'])<p class="mb-3 text-sm text-slate-600">{{ $manual['instructions'] }}</p>@endif

                                <p class="mb-2 text-sm font-semibold text-ink-900">Choose how you'd like to pay:</p>

                                {{-- Method chooser --}}
                                <div class="grid grid-cols-3 gap-2">
                                    @if ($mUpi)
                                        <button type="button" @click="choose('upi')" :class="sub==='upi' ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-slate-200 text-slate-600 hover:bg-slate-50'" class="flex flex-col items-center gap-1 rounded-xl border-2 px-3 py-3 text-xs font-bold transition">
                                            @if ($manual['upi_icon'])
                                                <img src="{{ $manual['upi_icon'] }}" alt="" class="h-6 w-auto object-contain">
                                            @else
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" /></svg>
                                            @endif
                                            UPI / QR
                                        </button>
                                    @endif
                                    @if ($mBank)
                                        <button type="button" @click="choose('bank')" :class="sub==='bank' ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-slate-200 text-slate-600 hover:bg-slate-50'" class="flex flex-col items-center gap-1 rounded-xl border-2 px-3 py-3 text-xs font-bold transition">
                                            @if ($manual['bank_icon'])
                                                <img src="{{ $manual['bank_icon'] }}" alt="" class="h-6 w-auto object-contain">
                                            @else
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11m16-11v11M8 14v3m4-3v3m4-3v3" /></svg>
                                            @endif
                                            Bank
                                        </button>
                                    @endif
                                    @if ($mCrypto)
                                        <button type="button" @click="choose('crypto')" :class="sub==='crypto' ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-slate-200 text-slate-600 hover:bg-slate-50'" class="flex flex-col items-center gap-1 rounded-xl border-2 px-3 py-3 text-xs font-bold transition">
                                            @if ($manual['crypto_icon'])
                                                <img src="{{ $manual['crypto_icon'] }}" alt="" class="h-6 w-auto object-contain">
                                            @else
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm-2.25-9h4.5m-4.5 0V8.25m0 3.75v3.75m4.5-3.75V8.25m0 3.75v3.75M9 7.5h4.125a1.875 1.875 0 0 1 0 3.75H9m0 0h4.5a1.875 1.875 0 0 1 0 3.75H9" /></svg>
                                            @endif
                                            Crypto
                                        </button>
                                    @endif
                                </div>

                                {{-- Prompt shown until a method is chosen --}}
                                <div x-show="sub === ''" class="mt-4 rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-500">👆 Select a payment method above to view the details and start your 10-minute payment window.</div>

                                {{-- Everything below appears only after a method is selected --}}
                                <div x-show="sub !== ''" x-cloak>
                                    {{-- Countdown --}}
                                    <div class="mt-4 flex items-center justify-between rounded-xl border px-4 py-3"
                                         :class="expired ? 'border-rose-200 bg-rose-50' : 'border-amber-200 bg-amber-50'">
                                        <span class="text-sm font-semibold" :class="expired ? 'text-rose-600' : 'text-amber-700'">
                                            <span x-show="!expired">⏳ Complete payment &amp; submit proof within</span>
                                            <span x-show="expired" x-cloak>⛔ Time expired — restart to continue</span>
                                        </span>
                                        <span class="font-display text-xl font-extrabold tabular-nums" :class="expired ? 'text-rose-600' : 'text-ink-900'" x-text="mm + ':' + ss">10:00</span>
                                    </div>

                                    {{-- Details per method --}}
                                    <div class="mt-4 space-y-4">
                                        @if ($mUpi)
                                            <div x-show="sub==='upi'" x-cloak class="grid gap-4 sm:grid-cols-2">
                                                <div class="rounded-lg border border-slate-200 bg-white p-3">
                                                    <p class="text-xs font-semibold text-slate-400">UPI ID</p>
                                                    <p class="font-mono text-sm text-ink-900">{{ $manual['upi'] }}</p>
                                                </div>
                                                @if ($manual['qr'])
                                                    <div class="rounded-lg border border-slate-200 bg-white p-3">
                                                        <p class="text-xs font-semibold text-slate-400">Scan to pay</p>
                                                        <img src="{{ $manual['qr'] }}" alt="UPI QR" class="mt-1 h-32 w-32 object-contain">
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        @if ($mBank)
                                            <div x-show="sub==='bank'" x-cloak class="rounded-lg border border-slate-200 bg-white p-3">
                                                <p class="text-xs font-semibold text-slate-400">Bank transfer</p>
                                                <p class="mt-1 whitespace-pre-line text-sm text-ink-900">{{ $manual['bank'] }}</p>
                                            </div>
                                        @endif
                                        @if ($mCrypto)
                                            <div x-show="sub==='crypto'" x-cloak class="grid gap-4 sm:grid-cols-2">
                                                <div class="rounded-lg border border-slate-200 bg-white p-3">
                                                    <p class="text-xs font-semibold text-slate-400">Crypto wallet address</p>
                                                    <ul class="mt-1 space-y-1">
                                                        @foreach ($manual['crypto'] as $w)
                                                            <li class="text-sm"><span class="font-semibold text-ink-900">{{ $w['label'] }}:</span> <span class="break-all font-mono text-slate-600">{{ $w['address'] }}</span></li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                @if ($manual['crypto_qr'])
                                                    <div class="rounded-lg border border-slate-200 bg-white p-3">
                                                        <p class="text-xs font-semibold text-slate-400">Scan to pay</p>
                                                        <img src="{{ $manual['crypto_qr'] }}" alt="Crypto QR" class="mt-1 h-32 w-32 object-contain">
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Proof (locked when expired) --}}
                                    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4 transition" :class="expired ? 'pointer-events-none opacity-50' : ''">
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div>
                                                <label for="transaction_id" class="label">Transaction ID</label>
                                                <input id="transaction_id" name="transaction_id" type="text" value="{{ old('transaction_id') }}" class="input" placeholder="Your payment reference">
                                            </div>
                                            <div>
                                                <label for="payment_proof" class="label">Payment screenshot</label>
                                                <input id="payment_proof" name="payment_proof" type="file" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-600 hover:file:bg-brand-100">
                                            </div>
                                        </div>
                                        <p class="mt-2 text-xs text-slate-400">Your order is verified by our team after payment. Downloads unlock once approved.</p>
                                    </div>

                                    {{-- Expired notice --}}
                                    <div x-show="expired" x-cloak class="mt-3 flex items-center justify-between rounded-xl bg-rose-50 px-4 py-3">
                                        <span class="text-sm font-semibold text-rose-600">Payment window expired.</span>
                                        <button type="button" @click="start()" class="btn-primary btn-sm">Restart timer</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
        </form>

        {{-- Summary (right) --}}
        <div class="lg:sticky lg:top-24 lg:self-start">
            <div class="card p-6">
                <h2 class="font-display text-lg font-bold text-ink-900">Your order</h2>
                <div class="mt-4 space-y-2">
                    @forelse ($items as $item)
                        <a href="{{ route('products.show', $item) }}" class="flex items-center gap-3 rounded-xl border border-slate-200 p-3 transition hover:border-brand-300 hover:bg-slate-50">
                            <img src="{{ $item->thumbnail_url }}" alt="" class="h-12 w-14 flex-shrink-0 rounded-lg object-cover">
                            <span class="min-w-0 flex-1 truncate text-sm font-medium text-ink-900">{{ $item->title }}</span>
                            <x-price :amount="$item->current_price" class="flex-shrink-0 text-sm font-bold text-ink-900" />
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">No items available for direct checkout.</p>
                    @endforelse
                </div>

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

                @if ($items->isNotEmpty() && ($total <= 0 || ! empty($methods)))
                    <button type="submit" form="checkoutForm" class="btn-primary btn-lg mt-6 w-full">{{ $total <= 0 ? 'Complete order' : 'Complete purchase' }}</button>
                    <p class="mt-3 text-center text-xs text-slate-400">By completing this purchase you agree to our terms of service.</p>
                @else
                    <a href="{{ route('products.index') }}" class="btn-ghost btn-lg mt-6 w-full">Browse more products</a>
                @endif
            </div>
        </div>
    </div>
</div>

@include('partials.ads', ['page' => 'checkout'])
@endsection
