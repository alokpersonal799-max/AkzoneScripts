@extends('layouts.install')

@section('install')
    <h1 class="font-display text-xl font-bold text-ink-900">Import database tables</h1>
    <p class="mt-1 text-sm text-slate-500">
        Great — the connection works! Choose whether to load demo content, then create the tables.
    </p>

    <form method="POST" action="{{ route('install.import.run') }}" class="mt-6" x-data="{ demo: true, loading: false }" @submit="loading = true">
        @csrf

        <label class="flex cursor-pointer items-start gap-3 rounded-xl border px-4 py-4 transition"
               :class="demo ? 'border-brand-300 bg-brand-50/60' : 'border-slate-200 bg-slate-50'">
            <input type="checkbox" name="demo" value="1" x-model="demo" class="mt-0.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
            <span>
                <span class="block text-sm font-semibold text-ink-900">Install demo data</span>
                <span class="block text-xs text-slate-500">Adds sample categories, products, gallery images and ratings so you can explore every feature straight away.</span>
            </span>
        </label>

        {{-- Demo ON: a ready-made admin is created, so account creation is skipped --}}
        <div x-show="demo" x-transition class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <p class="font-semibold">Demo admin login (created automatically)</p>
            <p class="mt-1">Email: <code class="rounded bg-white/70 px-1 font-mono">admin@akzone.com</code> &nbsp;·&nbsp; Password: <code class="rounded bg-white/70 px-1 font-mono">password</code></p>
            <p class="mt-1 text-xs text-emerald-700">You'll skip the account step and go straight to finish. Change this password after logging in.</p>
        </div>

        {{-- Demo OFF: fresh business install, you create your own admin + brand next --}}
        <div x-show="!demo" x-cloak x-transition class="mt-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
            <p class="font-semibold text-ink-900">Fresh business install</p>
            <p class="mt-1 text-xs">Your store starts empty (no sample products or reviews). Next you'll enter your <strong>brand name</strong> and create your own <strong>admin email &amp; password</strong>.</p>
        </div>

        <div class="mt-8 flex items-center justify-between">
            <a href="{{ route('install.database') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back</a>
            <button type="submit" :disabled="loading" class="btn-primary btn-lg disabled:opacity-60">
                <svg x-show="loading" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path></svg>
                <span x-text="loading ? 'Importing…' : (demo ? 'Import demo & finish' : 'Import & create admin')">Run import</span>
            </button>
        </div>
    </form>
@endsection
