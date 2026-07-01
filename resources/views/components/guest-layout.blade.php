<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-600 antialiased">
    {{-- Animated gradient background (large screens only, decorative) --}}
    <style>
        @keyframes authdrift1 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(60px,-40px) scale(1.15); } }
        @keyframes authdrift2 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(-50px,50px) scale(1.1); } }
        @keyframes authdrift3 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(40px,60px) scale(1.2); } }
        .auth-orb { position: absolute; border-radius: 9999px; filter: blur(70px); opacity: .55; }
    </style>
    <div class="pointer-events-none fixed inset-0 hidden overflow-hidden lg:block" aria-hidden="true">
        <div class="auth-orb" style="width:420px;height:420px;top:-80px;left:-60px;background:#93bbfd;animation:authdrift1 16s ease-in-out infinite;"></div>
        <div class="auth-orb" style="width:380px;height:380px;bottom:-90px;right:-40px;background:#c4b5fd;animation:authdrift2 20s ease-in-out infinite;"></div>
        <div class="auth-orb" style="width:300px;height:300px;top:40%;right:12%;background:#f9a8d4;animation:authdrift3 24s ease-in-out infinite;"></div>
    </div>

    <div class="relative flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="rounded-2xl bg-white p-8 shadow-soft ring-1 ring-slate-100 sm:p-10">
                {{-- Brand --}}
                <a href="{{ route('home') }}" class="flex items-center justify-center gap-2">
                    @include('partials.brand')
                </a>

                @isset($subtitle)
                    <p class="mt-3 text-center text-sm italic text-slate-400">{{ $subtitle }}</p>
                @endisset

                <div class="mt-8">
                    @include('partials.flash')
                    {{ $slot }}
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} {{ setting('site_name', config('app.name')) }}
            </p>
        </div>
    </div>
</body>
</html>
