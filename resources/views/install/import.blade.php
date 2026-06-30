@extends('layouts.install')

@section('install')
    <h1 class="font-display text-xl font-bold text-ink-900">Import database tables</h1>
    <p class="mt-1 text-sm text-slate-500">
        Great — the connection works! Click the button below to create all the database tables.
    </p>

    <form method="POST" action="{{ route('install.import.run') }}" class="mt-6" x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
            <input type="checkbox" name="demo" value="1" checked class="mt-0.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
            <span>
                <span class="block text-sm font-semibold text-ink-900">Install sample products</span>
                <span class="block text-xs text-slate-500">Adds demo categories and products so your store isn't empty. You can delete them later from the admin panel.</span>
            </span>
        </label>

        <div class="mt-8 flex items-center justify-between">
            <a href="{{ route('install.database') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back</a>
            <button type="submit" :disabled="loading" class="btn-primary btn-lg disabled:opacity-60">
                <svg x-show="loading" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path></svg>
                <span x-text="loading ? 'Importing…' : 'Run import'">Run import</span>
            </button>
        </div>
    </form>
@endsection
