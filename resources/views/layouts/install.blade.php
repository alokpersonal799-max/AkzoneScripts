<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-ink-900 font-sans text-slate-300 antialiased">

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
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-400 to-indigo-500 font-display text-xl font-extrabold text-ink-900 shadow-glow">A</span>
        <span class="font-display text-2xl font-bold text-white">Akzone<span class="text-brand-400">Scripts</span></span>
    </div>
    <p class="mt-2 text-center text-sm text-slate-400">Installation Wizard</p>

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
                        {{ $active ? 'bg-brand-400/15 text-brand-300' : ($done ? 'text-emerald-300' : 'text-slate-500') }}">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold
                            {{ $active ? 'bg-brand-400 text-ink-900' : ($done ? 'bg-emerald-500 text-white' : 'bg-white/10 text-slate-400') }}">
                            @if ($done)
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            @else
                                {{ $num }}
                            @endif
                        </span>
                        <span class="hidden font-medium sm:inline">{{ $label }}</span>
                    </span>
                    @if (! $loop->last)
                        <span class="h-px w-4 bg-white/10 sm:w-6"></span>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>

    {{-- Card --}}
    <div class="mt-8 rounded-2xl border border-white/5 bg-ink-800 p-6 sm:p-8">
        @if (session('error'))
            <div class="mb-6 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-rose-300">
                <ul class="list-inside list-disc text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('install')
    </div>

    <p class="mt-6 text-center text-xs text-slate-600">&copy; {{ date('Y') }} AkzoneScripts</p>
</div>
</body>
</html>
