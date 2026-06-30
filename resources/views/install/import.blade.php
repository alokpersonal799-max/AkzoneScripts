@extends('layouts.install')

@section('install')
    <h1 class="font-display text-xl font-bold text-white">Import database tables</h1>
    <p class="mt-1 text-sm text-slate-400">
        Great — the connection works! Click the button below to create all the database tables.
    </p>

    <form method="POST" action="{{ route('install.import.run') }}" class="mt-6" x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-ink-900 px-4 py-4 cursor-pointer">
            <input type="checkbox" name="demo" value="1" checked class="mt-0.5 rounded border-white/20 bg-ink-800 text-brand-500 focus:ring-brand-400/30">
            <span>
                <span class="block text-sm font-medium text-white">Install sample products</span>
                <span class="block text-xs text-slate-400">Adds demo categories and products so your store isn't empty. You can delete them later from the admin panel.</span>
            </span>
        </label>

        <div class="mt-8 flex items-center justify-between">
            <a href="{{ route('install.database') }}" class="text-sm text-slate-400 hover:text-brand-300">&larr; Back</a>
            <button type="submit" :disabled="loading"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand-400 px-6 py-2.5 font-semibold text-ink-900 transition hover:bg-brand-300 disabled:opacity-60">
                <svg x-show="loading" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path></svg>
                <span x-text="loading ? 'Importing…' : 'Run import'">Run import</span>
            </button>
        </div>
    </form>
@endsection
