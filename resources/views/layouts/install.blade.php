<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head')
    <style>
        @keyframes instfloat { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(30px,-24px) scale(1.12); } }
        .inst-orb { position: absolute; border-radius: 9999px; filter: blur(80px); opacity: .45; }
    </style>
</head>
<body class="relative min-h-screen overflow-x-hidden bg-slate-100 font-sans text-slate-600 antialiased">

@php
    $steps = [
        1 => 'Requirements',
        2 => 'Permissions',
        3 => 'Database',
        4 => 'Setup Mode',
        5 => 'Admin Account',
        6 => 'Completed',
    ];
    $current = $step ?? 1;
    $pct = max(0, min(100, (($current - 1) / (count($steps) - 1)) * 100));
@endphp

{{-- Decorative background --}}
<div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
    <div class="inst-orb" style="width:460px;height:460px;top:-120px;left:-90px;background:#93bbfd;animation:instfloat 20s ease-in-out infinite;"></div>
    <div class="inst-orb" style="width:420px;height:420px;bottom:-120px;right:-80px;background:#c4b5fd;animation:instfloat 26s ease-in-out infinite reverse;"></div>
</div>

<div class="relative mx-auto flex min-h-screen max-w-3xl flex-col px-4 py-8 sm:px-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-indigo-500 font-display text-xl font-extrabold text-white shadow-lift">A</span>
            <div class="leading-tight">
                <span class="font-display text-xl font-extrabold text-ink-900">Akzone<span class="text-brand-600">Scripts</span></span>
                <p class="text-xs text-slate-500">Installation Wizard</p>
            </div>
        </div>
        <a href="{{ route('install.manual') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-brand-300 hover:text-brand-600">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
            Manual
        </a>
    </div>

    {{-- Step progress with connecting bar --}}
    <div class="mt-8">
        <div class="relative">
            <div class="absolute left-0 right-0 top-4 h-0.5 bg-slate-200"></div>
            <div class="absolute left-0 top-4 h-0.5 bg-gradient-to-r from-brand-500 to-indigo-500 transition-all duration-500" style="width: {{ $pct }}%"></div>
            <ol class="relative flex justify-between">
                @foreach ($steps as $num => $label)
                    @php $done = $num < $current; $active = $num === $current; @endphp
                    <li class="flex flex-col items-center gap-1.5">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold ring-4 ring-slate-100
                            {{ $active ? 'bg-brand-600 text-white' : ($done ? 'bg-emerald-500 text-white' : 'bg-white text-slate-400 ring-slate-100') }}">
                            @if ($done)
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            @else
                                {{ $num }}
                            @endif
                        </span>
                        <span class="hidden text-[11px] font-semibold sm:block {{ $active ? 'text-brand-700' : ($done ? 'text-emerald-600' : 'text-slate-400') }}">{{ $label }}</span>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>

    {{-- Card --}}
    <div class="mt-8 rounded-3xl border border-white/60 bg-white/90 p-6 shadow-xl backdrop-blur sm:p-8">
        @if (session('error'))
            <div class="mb-6 flex items-start gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <svg class="mt-0.5 h-4 w-4 flex-none" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
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

    <p class="mt-6 text-center text-xs text-slate-400">&copy; {{ date('Y') }} AkzoneScripts &middot; <a href="{{ route('install.manual') }}" class="font-semibold text-slate-500 hover:text-brand-600">Setup Manual</a></p>
</div>
</body>
</html>
