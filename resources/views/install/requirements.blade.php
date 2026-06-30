@extends('layouts.install')

@section('install')
    <h1 class="font-display text-xl font-bold text-white">Server requirements</h1>
    <p class="mt-1 text-sm text-slate-400">We're checking that your server has everything needed to run AkzoneScripts.</p>

    <div class="mt-6 space-y-2">
        {{-- PHP version --}}
        <div class="flex items-center justify-between rounded-xl border border-white/5 bg-ink-900 px-4 py-3">
            <span class="text-sm text-slate-200">PHP &ge; 8.2 <span class="text-slate-500">(you have {{ $phpVersion }})</span></span>
            @include('install.partials.check', ['ok' => $phpOk])
        </div>

        {{-- Extensions --}}
        @foreach ($extensions as $name => $ok)
            <div class="flex items-center justify-between rounded-xl border border-white/5 bg-ink-900 px-4 py-3">
                <span class="text-sm text-slate-200">{{ $name }} extension</span>
                @include('install.partials.check', ['ok' => $ok])
            </div>
        @endforeach
    </div>

    <div class="mt-8 flex items-center justify-between">
        @if ($passed)
            <p class="text-sm text-emerald-300">All requirements met. You're good to go!</p>
            <a href="{{ route('install.permissions') }}" class="rounded-xl bg-brand-400 px-6 py-2.5 font-semibold text-ink-900 transition hover:bg-brand-300">Continue &rarr;</a>
        @else
            <p class="text-sm text-rose-300">Please install the missing requirements, then refresh.</p>
            <a href="{{ route('install.requirements') }}" class="rounded-xl border border-white/10 px-6 py-2.5 font-medium text-slate-300 hover:bg-white/5">Re-check</a>
        @endif
    </div>
@endsection
