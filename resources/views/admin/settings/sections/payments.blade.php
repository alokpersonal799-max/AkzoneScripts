<div class="card p-6 sm:p-8">
    <form method="POST" action="{{ route('admin.settings.payments') }}" class="space-y-6">
        @csrf @method('PUT')
        <h2 class="font-display text-lg font-bold text-ink-900">Payment gateways</h2>
        <p class="text-sm text-slate-500">Toggle which methods appear at checkout. Disabled gateways are hidden from customers.</p>

        {{-- Manual --}}
        <div class="rounded-xl border border-slate-200 p-4">
            <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                <input type="checkbox" name="pay_manual_enabled" value="1" {{ setting('pay_manual_enabled', '1') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                Manual / Offline payment (UPI, bank, crypto, QR)
            </label>
            <p class="mt-1 pl-6 text-xs text-slate-400">Configure the details under the "Manual Payment" section.</p>
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
        </div>

        <p class="rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700">Note: Stripe/PayPal/Razorpay show at checkout when enabled. Wire their SDKs to capture live payments; manual payment works fully out of the box with admin verification.</p>

        <button type="submit" class="btn-primary btn-md">Save payment settings</button>
    </form>
</div>
