@extends('layouts.install')

@section('install')
    <h1 class="font-display text-xl font-bold text-ink-900">Database connection</h1>
    <p class="mt-1 text-sm text-slate-500">
        Enter your database details. Create an empty MySQL database first
        (in cPanel/phpMyAdmin on hosting, or phpMyAdmin on XAMPP). Do not use <code>#</code> or spaces.
    </p>

    <form method="POST" action="{{ route('install.database.save') }}" class="mt-6 space-y-5">
        @csrf

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="host" class="label">Database host <span class="text-rose-500">*</span></label>
                <input id="host" name="host" type="text" value="{{ old('host', $config['host']) }}" required class="input">
                <p class="mt-1 text-xs text-slate-400">Usually <code>localhost</code> or <code>127.0.0.1</code></p>
            </div>
            <div>
                <label for="port" class="label">Port <span class="text-rose-500">*</span></label>
                <input id="port" name="port" type="text" value="{{ old('port', $config['port']) }}" required class="input">
                <p class="mt-1 text-xs text-slate-400">Default MySQL port is <code>3306</code></p>
            </div>
        </div>

        <div>
            <label for="database" class="label">Database name <span class="text-rose-500">*</span></label>
            <input id="database" name="database" type="text" value="{{ old('database', $config['database']) }}" required class="input">
        </div>

        <div>
            <label for="username" class="label">Database username <span class="text-rose-500">*</span></label>
            <input id="username" name="username" type="text" value="{{ old('username', $config['username']) }}" required class="input">
            <p class="mt-1 text-xs text-slate-400">On XAMPP this is usually <code>root</code></p>
        </div>

        <div>
            <label for="password" class="label">Database password</label>
            <input id="password" name="password" type="text" value="{{ old('password') }}" class="input">
            <p class="mt-1 text-xs text-slate-400">On XAMPP this is usually empty. Leave blank if there's no password.</p>
        </div>

        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('install.permissions') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back</a>
            <button type="submit" class="btn-primary btn-lg">Test &amp; continue &rarr;</button>
        </div>
    </form>
@endsection
