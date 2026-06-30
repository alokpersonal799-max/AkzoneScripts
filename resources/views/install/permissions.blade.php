@extends('layouts.install')

@section('install')
    <h1 class="font-display text-xl font-bold text-ink-900">Folder permissions</h1>
    <p class="mt-1 text-sm text-slate-500">These files and folders must be writable so the app can save settings and run.</p>

    <div class="mt-6 space-y-2">
        @foreach ($paths as $path => $ok)
            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <span class="font-mono text-sm text-slate-700">{{ $path }}</span>
                @include('install.partials.check', ['ok' => $ok])
            </div>
        @endforeach
    </div>

    <div class="mt-8 flex items-center justify-between">
        <a href="{{ route('install.requirements') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back</a>
        @if ($passed)
            <a href="{{ route('install.database') }}" class="btn-primary btn-lg">Continue &rarr;</a>
        @else
            <a href="{{ route('install.permissions') }}" class="btn-ghost btn-md">Re-check</a>
        @endif
    </div>

    @unless ($passed)
        <p class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-xs text-amber-700">
            Tip: set these folders to permission <strong>755</strong> (or <strong>775</strong>) in your hosting file manager,
            and make sure <code>.env</code> is writable.
        </p>
    @endunless
@endsection
