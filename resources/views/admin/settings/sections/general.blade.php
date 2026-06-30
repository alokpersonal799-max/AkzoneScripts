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

        <div class="grid gap-5 sm:grid-cols-2">
            @foreach (['social_twitter' => 'Twitter URL', 'social_github' => 'GitHub URL', 'social_discord' => 'Discord URL', 'social_facebook' => 'Facebook URL'] as $key => $label)
                <div><label for="{{ $key }}" class="label">{{ $label }}</label><input id="{{ $key }}" name="{{ $key }}" type="text" value="{{ old($key, setting($key)) }}" class="input"></div>
            @endforeach
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div><label for="contact_whatsapp" class="label">Global WhatsApp number <span class="text-slate-400">(country code, no +)</span></label><input id="contact_whatsapp" name="contact_whatsapp" type="text" value="{{ old('contact_whatsapp', setting('contact_whatsapp')) }}" class="input" placeholder="14155552671"></div>
            <div><label for="contact_telegram" class="label">Global Telegram username <span class="text-slate-400">(no @)</span></label><input id="contact_telegram" name="contact_telegram" type="text" value="{{ old('contact_telegram', setting('contact_telegram')) }}" class="input" placeholder="yourchannel"></div>
        </div>

        <div class="rounded-xl border border-slate-200 p-4">
            <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                <input type="checkbox" name="announcement_enabled" value="1" {{ setting('announcement_enabled') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                Show announcement bar
            </label>
            <input name="announcement_text" type="text" value="{{ old('announcement_text', setting('announcement_text')) }}" class="input mt-3" placeholder="Announcement text...">
        </div>

        <button type="submit" class="btn-primary btn-md">Save general settings</button>
    </form>
</div>
