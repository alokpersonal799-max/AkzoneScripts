<div class="card p-6 sm:p-8">
    <form method="POST" action="{{ route('admin.settings.auth') }}" class="space-y-5">
        @csrf @method('PUT')
        <h2 class="font-display text-lg font-bold text-ink-900">Login, verification &amp; captcha</h2>

        <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 text-sm">
            <input type="checkbox" name="require_email_verification" value="1" {{ setting('require_email_verification') === '1' ? 'checked' : '' }} class="mt-0.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
            <span><span class="font-semibold text-ink-900">Require email verification</span><br><span class="text-xs text-slate-400">If on, new users must verify their email before signing in. If off, they can log in immediately. You can also verify users manually from the Customers page.</span></span>
        </label>

        <div class="border-t border-slate-100 pt-5">
            <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                <input type="checkbox" name="captcha_enabled" value="1" {{ setting('captcha_enabled') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                Enable Google reCAPTCHA (v2) on login &amp; register
            </label>
            <div class="mt-4 grid gap-5 sm:grid-cols-2">
                <div><label class="label">Site key</label><input name="captcha_site_key" value="{{ old('captcha_site_key', setting('captcha_site_key')) }}" class="input"></div>
                <div><label class="label">Secret key</label><input name="captcha_secret" value="{{ old('captcha_secret', setting('captcha_secret')) }}" class="input"></div>
            </div>
            <p class="mt-2 text-xs text-slate-400">Get keys from Google reCAPTCHA admin console (reCAPTCHA v2 "I'm not a robot").</p>
        </div>

        <button type="submit" class="btn-primary btn-md">Save login settings</button>
    </form>
</div>
