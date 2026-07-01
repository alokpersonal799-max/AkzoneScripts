<div class="card p-6 sm:p-8">
    <form method="POST" action="{{ route('admin.settings.general') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')
        <h2 class="font-display text-lg font-bold text-ink-900">Branding &amp; general</h2>

        <div class="flex items-center gap-4">
            <span class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl bg-slate-100">
                @if (setting('site_logo'))
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(setting('site_logo')) }}" alt="logo" class="h-full w-full object-contain">
                @else
                    <span class="font-display text-2xl font-extrabold text-brand-600">A</span>
                @endif
            </span>
            <div>
                <label for="logo" class="label">Site logo</label>
                <input id="logo" name="logo" type="file" accept="image/*" class="block text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-600 hover:file:bg-brand-100">
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div><label for="site_name" class="label">Site name</label><input id="site_name" name="site_name" type="text" value="{{ old('site_name', setting('site_name')) }}" required class="input"></div>
            <div><label for="support_email" class="label">Support email</label><input id="support_email" name="support_email" type="email" value="{{ old('support_email', setting('support_email')) }}" required class="input"></div>
        </div>

        <div>
            <label for="timezone" class="label">Timezone</label>
            @php $currentTz = old('timezone', setting('timezone', config('app.timezone', 'UTC'))); @endphp
            <select id="timezone" name="timezone" class="input">
                @foreach (timezone_identifiers_list() as $tz)
                    <option value="{{ $tz }}" {{ $currentTz === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-400">Controls dates &amp; times shown across the store and admin (e.g. orders, reports).</p>
        </div>

        <div>
            <label for="portfolio_url" class="label">Portfolio link <span class="text-slate-400">(optional)</span></label>
            <input id="portfolio_url" name="portfolio_url" type="url" value="{{ old('portfolio_url', setting('portfolio_url')) }}" class="input" placeholder="https://your-portfolio.com">
            <p class="mt-1 text-xs text-slate-400">Shown as a "View Portfolio" button on the Services page.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            @foreach (['social_twitter' => 'Twitter URL', 'social_github' => 'GitHub URL', 'social_discord' => 'Discord URL', 'social_facebook' => 'Facebook URL'] as $key => $label)
                <div><label for="{{ $key }}" class="label">{{ $label }}</label><input id="{{ $key }}" name="{{ $key }}" type="text" value="{{ old($key, setting($key)) }}" class="input"></div>
            @endforeach
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div><label for="contact_whatsapp" class="label">Global WhatsApp number <span class="text-slate-400">(country code, no +)</span></label><input id="contact_whatsapp" name="contact_whatsapp" type="text" value="{{ old('contact_whatsapp', setting('contact_whatsapp')) }}" class="input" placeholder="14155552671"></div>
            <div><label for="contact_telegram" class="label">Global Telegram username <span class="text-slate-400">(no @)</span></label><input id="contact_telegram" name="contact_telegram" type="text" value="{{ old('contact_telegram', setting('contact_telegram')) }}" class="input" placeholder="yourchannel"></div>
        </div>

        {{-- Installable app (PWA) visibility --}}
        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
            <h3 class="flex items-center gap-2 text-sm font-bold text-ink-900">
                <svg class="h-4 w-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                Installable app (PWA)
            </h3>
            <p class="mt-1 text-xs text-slate-500">Controls the floating &ldquo;Install app&rdquo; button. It only appears on browsers that support installation.</p>

            <div class="mt-4 space-y-3">
                <label class="flex items-center gap-3">
                    <input type="hidden" name="pwa_install_enabled" value="0">
                    <input type="checkbox" name="pwa_install_enabled" value="1" @checked(setting('pwa_install_enabled', '1') === '1') class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm font-medium text-slate-700">Enable the install-app button</span>
                </label>
                <label class="flex items-center gap-3">
                    <input type="hidden" name="pwa_install_mobile" value="0">
                    <input type="checkbox" name="pwa_install_mobile" value="1" @checked(setting('pwa_install_mobile', '1') === '1') class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm font-medium text-slate-700">Show on phones <span class="text-slate-400">(small screens)</span></span>
                </label>
                <label class="flex items-center gap-3">
                    <input type="hidden" name="pwa_install_desktop" value="0">
                    <input type="checkbox" name="pwa_install_desktop" value="1" @checked(setting('pwa_install_desktop', '1') === '1') class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm font-medium text-slate-700">Show on tablets, laptops &amp; computers <span class="text-slate-400">(large screens)</span></span>
                </label>
            </div>
        </div>

        <button type="submit" class="btn-primary btn-md">Save general settings</button>
    </form>
</div>
