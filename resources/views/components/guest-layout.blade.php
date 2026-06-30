<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-600 antialiased">
    <div class="flex min-h-screen">
        {{-- Left: form --}}
        <div class="flex w-full flex-col justify-center px-6 py-12 lg:w-1/2 lg:px-20">
            <div class="mx-auto w-full max-w-md">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-indigo-500 font-display text-xl font-extrabold text-white shadow-lift">A</span>
                    <span class="font-display text-2xl font-extrabold text-ink-900">Akzone<span class="text-brand-600">Scripts</span></span>
                </a>

                <div class="mt-10">
                    @include('partials.flash')
                    {{ $slot }}
                </div>
            </div>
        </div>

        {{-- Right: brand panel --}}
        <div class="relative hidden w-1/2 overflow-hidden bg-ink-900 lg:block">
            <div class="dot-grid absolute inset-0 opacity-60"></div>
            <div class="absolute -right-20 top-20 h-72 w-72 rounded-full bg-brand-600/30 blur-3xl"></div>
            <div class="absolute -left-10 bottom-10 h-72 w-72 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="relative flex h-full flex-col justify-center px-16">
                <h2 class="font-display text-4xl font-extrabold leading-tight text-white">
                    The marketplace for <span class="gradient-text">modern builders.</span>
                </h2>
                <p class="mt-6 max-w-md text-lg text-slate-300">
                    Buy and download premium scripts, source code and design assets. Instant delivery, lifetime access, and updates included.
                </p>
                <ul class="mt-10 space-y-4">
                    @foreach (['Instant digital downloads', 'Verified, production-ready code', 'Secure checkout & lifetime access'] as $point)
                        <li class="flex items-center gap-3 text-slate-200">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand-500/20 text-brand-300">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            </span>
                            {{ $point }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
