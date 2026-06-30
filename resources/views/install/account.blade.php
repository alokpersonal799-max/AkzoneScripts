@extends('layouts.install')

@section('install')
    <h1 class="font-display text-xl font-bold text-ink-900">Site &amp; admin account</h1>
    <p class="mt-1 text-sm text-slate-500">Enter your website details and the admin login you'll use to manage the store.</p>

    <form method="POST" action="{{ route('install.account.save') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <label for="site_name" class="label">Website name <span class="text-rose-500">*</span></label>
            <input id="site_name" name="site_name" type="text" value="{{ old('site_name', 'AkzoneScripts') }}" required class="input">
        </div>

        <div>
            <label for="site_url" class="label">Website URL <span class="text-rose-500">*</span></label>
            <input id="site_url" name="site_url" type="url" value="{{ old('site_url', $siteUrl) }}" required class="input">
            <p class="mt-1 text-xs text-slate-400">Example: <code>https://yourdomain.com</code> — no trailing slash. The admin panel will be at this URL + <code>/admin</code></p>
        </div>

        <hr class="border-slate-100">

        <div>
            <label for="admin_name" class="label">Admin name <span class="text-rose-500">*</span></label>
            <input id="admin_name" name="admin_name" type="text" value="{{ old('admin_name') }}" required class="input">
        </div>

        <div>
            <label for="admin_email" class="label">Admin email <span class="text-rose-500">*</span></label>
            <input id="admin_email" name="admin_email" type="email" value="{{ old('admin_email') }}" required class="input">
            <p class="mt-1 text-xs text-slate-400">You'll log in with this email.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="admin_password" class="label">Admin password <span class="text-rose-500">*</span></label>
                <input id="admin_password" name="admin_password" type="password" required class="input">
                <p class="mt-1 text-xs text-slate-400">At least 8 characters.</p>
            </div>
            <div>
                <label for="admin_password_confirmation" class="label">Confirm password <span class="text-rose-500">*</span></label>
                <input id="admin_password_confirmation" name="admin_password_confirmation" type="password" required class="input">
            </div>
        </div>

        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('install.import') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back</a>
            <button type="submit" class="btn-primary btn-lg">Finish install &rarr;</button>
        </div>
    </form>
@endsection
