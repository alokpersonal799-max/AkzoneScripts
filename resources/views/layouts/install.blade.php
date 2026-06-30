<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-600 antialiased">

@php
    $steps = [
        1 => 'Requirements',
        2 => 'Permissions',
        3 => 'Database',
        4 => 'Import',
        5 => 'Site & Admin',
        6 => 'Completed',
    ];
    $current = $step ?? 1;
@endphp

<div class="mx-auto flex min-h-screen max-w-4xl flex-col px-4 py-10 sm:px-6">
    {{-- Brand --}}
    <div class="flex items-center justify-center gap-2">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-indigo-500 font-display text-xl font-extrabold text-white shadow-lift">A</span>
        <span class="font-display text-2xl font-extrabold text-ink-900">Akzone<span class="text-brand-600">Scripts</span></span>
    </div>
    <p class="mt-2 text-center text-sm text-slate-500">Installation Wizard</p>

    {{-- Step progress --}}
    <div class="mt-8 overflow-x-auto">
        <ol class="flex min-w-max items-center justify-center gap-1 text-sm sm:gap-2">
            @foreach ($steps as $num => $label)
                @php
                    $done = $num < $current;
                    $active = $num === $current;
                @endphp
                <li class="flex items-center gap-2">
                    <span class="flex items-center gap-2 rounded-full px-3 py-1.5
                        {{ $active ? 'bg-brand-50 text-brand-700' : ($done ? 'text-emerald-600' : 'text-slate-400') }}">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold
                            {{ $active ? 'bg-brand-600 text-white' : ($done ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500') }}">
                            @if ($done)
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            @else
                                {{ $num }}
                            @endif
                        </span>
                        <span class="hidden font-semibold sm:inline">{{ $label }}</span>
                    </span>
                    @if (! $loop->last)
                        <span class="h-px w-4 bg-slate-200 sm:w-6"></span>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>

    {{-- Card --}}
    <div class="card mt-8 p-6 sm:p-8">
        @if (session('error'))
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700">
                <ul class="list-inside list-disc text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('install')
    </div>

    <p class="mt-6 text-center text-xs text-slate-400">&copy; {{ date('Y') }} AkzoneScripts</p>
</div>
</body>
</html>
