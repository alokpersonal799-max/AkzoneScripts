@extends('layouts.admin')

@section('page-title', 'Site Settings')

@section('admin')
<div x-data="{ tab: '{{ request('tab', 'general') }}' }" class="space-y-6">
    {{-- Tabs --}}
    <div class="flex flex-wrap gap-2">
        @foreach (['general' => 'General', 'hero' => 'Hero Section', 'footer' => 'Footer', 'currencies' => 'Currencies'] as $key => $label)
            <button @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}' ? 'bg-brand-600 text-white' : 'bg-white text-slate-600 border border-slate-200'"
                    class="rounded-xl px-4 py-2 text-sm font-semibold transition">{{ $label }}</button>
        @endforeach
    </div>

    {{-- General --}}
    <div x-show="tab === 'general'" class="card p-6 sm:p-8">
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
                <div>
                    <label for="site_name" class="label">Site name</label>
                    <input id="site_name" name="site_name" type="text" value="{{ old('site_name', setting('site_name')) }}" required class="input">
                </div>
                <div>
                    <label for="support_email" class="label">Support email</label>
                    <input id="support_email" name="support_email" type="email" value="{{ old('support_email', setting('support_email')) }}" required class="input">
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                @foreach (['social_twitter' => 'Twitter URL', 'social_github' => 'GitHub URL', 'social_discord' => 'Discord URL', 'social_facebook' => 'Facebook URL'] as $key => $label)
                    <div>
                        <label for="{{ $key }}" class="label">{{ $label }}</label>
                        <input id="{{ $key }}" name="{{ $key }}" type="text" value="{{ old($key, setting($key)) }}" class="input">
                    </div>
                @endforeach
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="contact_whatsapp" class="label">WhatsApp number <span class="text-slate-400">(with country code, no +)</span></label>
                    <input id="contact_whatsapp" name="contact_whatsapp" type="text" value="{{ old('contact_whatsapp', setting('contact_whatsapp')) }}" class="input" placeholder="14155552671">
                </div>
                <div>
                    <label for="contact_telegram" class="label">Telegram username <span class="text-slate-400">(without @)</span></label>
                    <input id="contact_telegram" name="contact_telegram" type="text" value="{{ old('contact_telegram', setting('contact_telegram')) }}" class="input" placeholder="yourchannel">
                </div>
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

    {{-- Hero --}}
    <div x-show="tab === 'hero'" x-cloak class="card p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.settings.hero') }}" class="space-y-5">
            @csrf @method('PUT')
            <h2 class="font-display text-lg font-bold text-ink-900">Hero section</h2>
            <p class="text-sm text-slate-500">Edit the headline shown at the top of your homepage.</p>

            <div>
                <label for="hero_badge" class="label">Badge text</label>
                <input id="hero_badge" name="hero_badge" type="text" value="{{ old('hero_badge', setting('hero_badge')) }}" class="input">
            </div>
            <div>
                <label for="hero_title" class="label">Heading <span class="text-slate-400">(the stat count is added automatically)</span></label>
                <input id="hero_title" name="hero_title" type="text" value="{{ old('hero_title', setting('hero_title')) }}" required class="input">
            </div>
            <div>
                <label for="hero_highlight" class="label">Highlighted words <span class="text-slate-400">(shown in gradient — must appear in the heading)</span></label>
                <input id="hero_highlight" name="hero_highlight" type="text" value="{{ old('hero_highlight', setting('hero_highlight')) }}" class="input">
            </div>
            <div>
                <label for="hero_subtitle" class="label">Subtitle</label>
                <textarea id="hero_subtitle" name="hero_subtitle" rows="3" class="input">{{ old('hero_subtitle', setting('hero_subtitle')) }}</textarea>
            </div>

            <button type="submit" class="btn-primary btn-md">Save hero section</button>
        </form>
    </div>

    {{-- Footer --}}
    <div x-show="tab === 'footer'" x-cloak class="card p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.settings.footer') }}" class="space-y-5">
            @csrf @method('PUT')
            <h2 class="font-display text-lg font-bold text-ink-900">Footer</h2>

            <div>
                <label for="footer_about" class="label">About text</label>
                <textarea id="footer_about" name="footer_about" rows="3" class="input">{{ old('footer_about', setting('footer_about')) }}</textarea>
            </div>
            <div>
                <label for="footer_copyright" class="label">Copyright line</label>
                <input id="footer_copyright" name="footer_copyright" type="text" value="{{ old('footer_copyright', setting('footer_copyright')) }}" class="input">
            </div>

            <button type="submit" class="btn-primary btn-md">Save footer</button>
        </form>
    </div>

    {{-- Currencies --}}
    <div x-show="tab === 'currencies'" x-cloak>
        @include('admin.currencies._manage')
    </div>
</div>
@endsection
