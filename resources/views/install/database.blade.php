@extends('layouts.install')

@section('install')
    <h1 class="font-display text-xl font-bold text-white">Database connection</h1>
    <p class="mt-1 text-sm text-slate-400">
        Enter your database details. Create an empty MySQL database first
        (in cPanel/phpMyAdmin on hosting, or phpMyAdmin on XAMPP). Do not use <code>#</code> or spaces.
    </p>

    <form method="POST" action="{{ route('install.database.save') }}" class="mt-6 space-y-5">
        @csrf

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="host" class="block text-sm font-medium text-slate-300">Database host <span class="text-rose-400">*</span></label>
                <input id="host" name="host" type="text" value="{{ old('host', $config['host']) }}" required
                       class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                <p class="mt-1 text-xs text-slate-500">Usually <code>localhost</code> or <code>127.0.0.1</code></p>
            </div>
            <div>
                <label for="port" class="block text-sm font-medium text-slate-300">Port <span class="text-rose-400">*</span></label>
                <input id="port" name="port" type="text" value="{{ old('port', $config['port']) }}" required
                       class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                <p class="mt-1 text-xs text-slate-500">Default MySQL port is <code>3306</code></p>
            </div>
        </div>

        <div>
            <label for="database" class="block text-sm font-medium text-slate-300">Database name <span class="text-rose-400">*</span></label>
            <input id="database" name="database" type="text" value="{{ old('database', $config['database']) }}" required
                   class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
        </div>

        <div>
            <label for="username" class="block text-sm font-medium text-slate-300">Database username <span class="text-rose-400">*</span></label>
            <input id="username" name="username" type="text" value="{{ old('username', $config['username']) }}" required
                   class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
            <p class="mt-1 text-xs text-slate-500">On XAMPP this is usually <code>root</code></p>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-300">Database password</label>
            <input id="password" name="password" type="text" value="{{ old('password') }}"
                   class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
            <p class="mt-1 text-xs text-slate-500">On XAMPP this is usually empty. Leave blank if there's no password.</p>
        </div>

        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('install.permissions') }}" class="text-sm text-slate-400 hover:text-brand-300">&larr; Back</a>
            <button type="submit" class="rounded-xl bg-brand-400 px-6 py-2.5 font-semibold text-ink-900 transition hover:bg-brand-300">Test &amp; continue &rarr;</button>
        </div>
    </form>
@endsection
