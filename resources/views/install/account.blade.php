@extends('layouts.install')

@section('install')
    <h1 class="font-display text-xl font-bold text-white">Site &amp; admin account</h1>
    <p class="mt-1 text-sm text-slate-400">Enter your website details and the admin login you'll use to manage the store.</p>

    <form method="POST" action="{{ route('install.account.save') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <label for="site_name" class="block text-sm font-medium text-slate-300">Website name <span class="text-rose-400">*</span></label>
            <input id="site_name" name="site_name" type="text" value="{{ old('site_name', 'AkzoneScripts') }}" required
                   class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
        </div>

        <div>
            <label for="site_url" class="block text-sm font-medium text-slate-300">Website URL <span class="text-rose-400">*</span></label>
            <input id="site_url" name="site_url" type="url" value="{{ old('site_url', $siteUrl) }}" required
                   class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
            <p class="mt-1 text-xs text-slate-500">Example: <code>https://yourdomain.com</code> — no trailing slash. The admin panel will be at this URL + <code>/admin</code></p>
        </div>

        <hr class="border-white/5">

        <div>
            <label for="admin_name" class="block text-sm font-medium text-slate-300">Admin name <span class="text-rose-400">*</span></label>
            <input id="admin_name" name="admin_name" type="text" value="{{ old('admin_name') }}" required
                   class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
        </div>

        <div>
            <label for="admin_email" class="block text-sm font-medium text-slate-300">Admin email <span class="text-rose-400">*</span></label>
            <input id="admin_email" name="admin_email" type="email" value="{{ old('admin_email') }}" required
                   class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
            <p class="mt-1 text-xs text-slate-500">You'll log in with this email.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="admin_password" class="block text-sm font-medium text-slate-300">Admin password <span class="text-rose-400">*</span></label>
                <input id="admin_password" name="admin_password" type="password" required
                       class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                <p class="mt-1 text-xs text-slate-500">At least 8 characters.</p>
            </div>
            <div>
                <label for="admin_password_confirmation" class="block text-sm font-medium text-slate-300">Confirm password <span class="text-rose-400">*</span></label>
                <input id="admin_password_confirmation" name="admin_password_confirmation" type="password" required
                       class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
            </div>
        </div>

        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('install.import') }}" class="text-sm text-slate-400 hover:text-brand-300">&larr; Back</a>
            <button type="submit" class="rounded-xl bg-brand-400 px-6 py-2.5 font-semibold text-ink-900 transition hover:bg-brand-300">Finish install &rarr;</button>
        </div>
    </form>
@endsection
