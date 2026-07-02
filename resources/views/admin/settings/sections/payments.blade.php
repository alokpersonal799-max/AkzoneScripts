<div class="card p-6 sm:p-8">
    <form method="POST" action="{{ route('admin.settings.payments') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')
        <h2 class="font-display text-lg font-bold text-ink-900">Payment gateways</h2>
        <p class="text-sm text-slate-500">Toggle which methods appear at checkout. Disabled gateways are hidden from customers. Upload a <strong>PNG</strong> icon to show a logo next to each method.</p>

        @php
            $payIcon = function ($key) {
                return setting($key) ? \Illuminate\Support\Facades\Storage::disk('public')->url(setting($key)) : null;
            };
        @endphp

        {{-- Manual --}}
        <div class="rounded-xl border border-slate-200 p-4">
            <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                <input type="checkbox" name="pay_manual_enabled" value="1" {{ setting('pay_manual_enabled', '1') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                Manual / Offline payment (UPI, bank, crypto, QR)
            </label>
            <p class="mt-1 pl-6 text-xs text-slate-400">Configure the details under the "Manual Payment" section.</p>
            <div class="mt-3 flex items-center gap-3 pl-6">
                <span class="flex h-10 w-16 items-center justify-center overflow-hidden rounded-lg bg-slate-100">@if ($payIcon('pay_manual_icon'))<img src="{{ $payIcon('pay_manual_icon') }}" class="h-full w-full object-contain">@else<span class="text-[10px] text-slate-400">PNG</span>@endif</span>
                <input name="pay_manual_icon" type="file" accept="image/png" class="block text-xs text-slate-500 file:mr-2 file:rounded file:border-0 file:bg-brand-50 file:px-2 file:py-1 file:text-xs file:font-semibold file:text-brand-600">
            </div>
        </div>

        {{-- Stripe --}}
        <div class="rounded-xl border border-slate-200 p-4">
            <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                <input type="checkbox" name="pay_stripe_enabled" value="1" {{ setting('pay_stripe_enabled') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                Stripe
            </label>
            <div class="mt-3 grid gap-3 pl-6 sm:grid-cols-2">
                <input name="stripe_key" value="{{ old('stripe_key', setting('stripe_key')) }}" class="input" placeholder="Publishable key">
                <input name="stripe_secret" value="{{ old('stripe_secret', setting('stripe_secret')) }}" class="input" placeholder="Secret key">
            </div>
            <div class="mt-3 flex items-center gap-3 pl-6">
                <span class="flex h-10 w-16 items-center justify-center overflow-hidden rounded-lg bg-slate-100">@if ($payIcon('pay_stripe_icon'))<img src="{{ $payIcon('pay_stripe_icon') }}" class="h-full w-full object-contain">@else<span class="text-[10px] text-slate-400">PNG</span>@endif</span>
                <input name="pay_stripe_icon" type="file" accept="image/png" class="block text-xs text-slate-500 file:mr-2 file:rounded file:border-0 file:bg-brand-50 file:px-2 file:py-1 file:text-xs file:font-semibold file:text-brand-600">
            </div>
        </div>

        {{-- PayPal --}}
        <div class="rounded-xl border border-slate-200 p-4">
            <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                <input type="checkbox" name="pay_paypal_enabled" value="1" {{ setting('pay_paypal_enabled') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                PayPal
            </label>
            <div class="mt-3 grid gap-3 pl-6 sm:grid-cols-2">
                <input name="paypal_client_id" value="{{ old('paypal_client_id', setting('paypal_client_id')) }}" class="input" placeholder="Client ID">
                <input name="paypal_secret" value="{{ old('paypal_secret', setting('paypal_secret')) }}" class="input" placeholder="Secret">
                <select name="paypal_mode" class="input">
                    <option value="sandbox" {{ setting('paypal_mode') === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                    <option value="live" {{ setting('paypal_mode') === 'live' ? 'selected' : '' }}>Live</option>
                </select>
            </div>
            <div class="mt-3 flex items-center gap-3 pl-6">
                <span class="flex h-10 w-16 items-center justify-center overflow-hidden rounded-lg bg-slate-100">@if ($payIcon('pay_paypal_icon'))<img src="{{ $payIcon('pay_paypal_icon') }}" class="h-full w-full object-contain">@else<span class="text-[10px] text-slate-400">PNG</span>@endif</span>
                <input name="pay_paypal_icon" type="file" accept="image/png" class="block text-xs text-slate-500 file:mr-2 file:rounded file:border-0 file:bg-brand-50 file:px-2 file:py-1 file:text-xs file:font-semibold file:text-brand-600">
            </div>
        </div>

        {{-- Razorpay --}}
        <div class="rounded-xl border border-slate-200 p-4">
            <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                <input type="checkbox" name="pay_razorpay_enabled" value="1" {{ setting('pay_razorpay_enabled') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                Razorpay
            </label>
            <div class="mt-3 grid gap-3 pl-6 sm:grid-cols-2">
                <input name="razorpay_key" value="{{ old('razorpay_key', setting('razorpay_key')) }}" class="input" placeholder="Key ID">
                <input name="razorpay_secret" value="{{ old('razorpay_secret', setting('razorpay_secret')) }}" class="input" placeholder="Key secret">
            </div>
            <div class="mt-3 flex items-center gap-3 pl-6">
                <span class="flex h-10 w-16 items-center justify-center overflow-hidden rounded-lg bg-slate-100">@if ($payIcon('pay_razorpay_icon'))<img src="{{ $payIcon('pay_razorpay_icon') }}" class="h-full w-full object-contain">@else<span class="text-[10px] text-slate-400">PNG</span>@endif</span>
                <input name="pay_razorpay_icon" type="file" accept="image/png" class="block text-xs text-slate-500 file:mr-2 file:rounded file:border-0 file:bg-brand-50 file:px-2 file:py-1 file:text-xs file:font-semibold file:text-brand-600">
            </div>
        </div>

        {{-- Coinbase Commerce (crypto) --}}
        <div class="rounded-xl border border-slate-200 p-4">
            <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                <input type="checkbox" name="pay_coinbase_enabled" value="1" {{ setting('pay_coinbase_enabled') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                Coinbase Commerce <span class="text-xs font-normal text-slate-400">(crypto — BTC, ETH, USDC…)</span>
            </label>
            <div class="mt-3 grid gap-3 pl-6">
                <input name="coinbase_key" value="{{ old('coinbase_key', setting('coinbase_key')) }}" class="input" placeholder="Coinbase Commerce API key">
            </div>
            <div class="mt-3 flex items-center gap-3 pl-6">
                <span class="flex h-10 w-16 items-center justify-center overflow-hidden rounded-lg bg-slate-100">@if ($payIcon('pay_coinbase_icon'))<img src="{{ $payIcon('pay_coinbase_icon') }}" class="h-full w-full object-contain">@else<span class="text-[10px] text-slate-400">PNG</span>@endif</span>
                <input name="pay_coinbase_icon" type="file" accept="image/png" class="block text-xs text-slate-500 file:mr-2 file:rounded file:border-0 file:bg-brand-50 file:px-2 file:py-1 file:text-xs file:font-semibold file:text-brand-600">
            </div>
        </div>

        <p class="rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700">Note: Stripe/PayPal/Razorpay/Coinbase show at checkout when enabled. Wire their SDKs to capture live payments; manual payment works fully out of the box with admin verification.</p>

        <button type="submit" class="btn-primary btn-md">Save payment settings</button>
    </form>
</div>
