@extends('layouts.install')

@section('install')
    <h1 class="font-display text-xl font-bold text-ink-900">Server requirements</h1>
    <p class="mt-1 text-sm text-slate-500">We're checking that your server has everything needed to run AkzoneScripts.</p>

    <div class="mt-6 space-y-2">
        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <span class="text-sm text-slate-700">PHP &ge; 8.2 <span class="text-slate-400">(you have {{ $phpVersion }})</span></span>
            @include('install.partials.check', ['ok' => $phpOk])
        </div>

        @foreach ($extensions as $name => $ok)
            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <span class="text-sm text-slate-700">{{ $name }} extension</span>
                @include('install.partials.check', ['ok' => $ok])
            </div>
        @endforeach

        @foreach ($recommended as $name => $ok)
            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <span class="text-sm text-slate-700">{{ $name }} extension <span class="ml-1 text-xs text-slate-400">(recommended)</span></span>
                @if ($ok)
                    @include('install.partials.check', ['ok' => true])
                @else
                    <span class="chip bg-amber-50 text-amber-700 ring-1 ring-amber-200">Optional</span>
                @endif
            </div>
        @endforeach
    </div>

    @php $missingRecommended = in_array(false, $recommended, true); @endphp
    @if ($passed && $missingRecommended)
        <p class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-xs text-amber-700">
            A recommended extension is missing, but it's optional — you can safely continue.
            To enable it on XAMPP, uncomment the matching <code>extension=...</code> line in <code>php.ini</code> and restart Apache.
        </p>
    @endif

    <div class="mt-8 flex items-center justify-between">
        @if ($passed)
            <p class="text-sm font-medium text-emerald-600">All requirements met. You're good to go!</p>
            <a href="{{ route('install.permissions') }}" class="btn-primary btn-lg">Continue &rarr;</a>
        @else
            <p class="text-sm text-rose-600">Please install the missing requirements, then refresh.</p>
            <a href="{{ route('install.requirements') }}" class="btn-ghost btn-md">Re-check</a>
        @endif
    </div>
@endsection
