@extends('layouts.install')

@section('install')
    <h1 class="font-display text-xl font-bold text-white">Folder permissions</h1>
    <p class="mt-1 text-sm text-slate-400">These files and folders must be writable so the app can save settings and run.</p>

    <div class="mt-6 space-y-2">
        @foreach ($paths as $path => $ok)
            <div class="flex items-center justify-between rounded-xl border border-white/5 bg-ink-900 px-4 py-3">
                <span class="font-mono text-sm text-slate-200">{{ $path }}</span>
                @include('install.partials.check', ['ok' => $ok])
            </div>
        @endforeach
    </div>

    <div class="mt-8 flex items-center justify-between">
        <a href="{{ route('install.requirements') }}" class="text-sm text-slate-400 hover:text-brand-300">&larr; Back</a>
        @if ($passed)
            <a href="{{ route('install.database') }}" class="rounded-xl bg-brand-400 px-6 py-2.5 font-semibold text-ink-900 transition hover:bg-brand-300">Continue &rarr;</a>
        @else
            <a href="{{ route('install.permissions') }}" class="rounded-xl border border-white/10 px-6 py-2.5 font-medium text-slate-300 hover:bg-white/5">Re-check</a>
        @endif
    </div>

    @unless ($passed)
        <p class="mt-4 rounded-xl bg-amber-500/10 px-4 py-3 text-xs text-amber-300">
            Tip: set these folders to permission <strong>755</strong> (or <strong>775</strong>) in your hosting file manager,
            and make sure <code>.env</code> is writable.
        </p>
    @endunless
@endsection
