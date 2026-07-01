<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-600 antialiased">
    {{-- Animated falling marketplace icons (large screens only, purely decorative) --}}
    <style>
        @keyframes authfall {
            0%   { transform: translateY(-12vh) rotate(0deg); opacity: 0; }
            10%  { opacity: .85; }
            90%  { opacity: .85; }
            100% { transform: translateY(112vh) rotate(360deg); opacity: 0; }
        }
        @keyframes authglow { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(40px,30px) scale(1.15); } }
        .auth-bg-glow { position: absolute; border-radius: 9999px; filter: blur(90px); opacity: .5; }
        .auth-fall-item {
            position: absolute;
            top: -10vh;
            user-select: none;
            will-change: transform, opacity;
            animation-name: authfall;
            animation-timing-function: linear;
            animation-iteration-count: infinite;
        }
    </style>
    <div class="pointer-events-none fixed inset-0 hidden overflow-hidden lg:block" aria-hidden="true">
        {{-- soft colour wash behind the falling icons --}}
        <div class="auth-bg-glow" style="width:460px;height:460px;top:-90px;left:-70px;background:#93bbfd;animation:authglow 18s ease-in-out infinite;"></div>
        <div class="auth-bg-glow" style="width:400px;height:400px;bottom:-100px;right:-50px;background:#c4b5fd;animation:authglow 22s ease-in-out infinite reverse;"></div>
        @php
            $authGlyphs = ['🛒', '💻', '⭐', '🎨', '📦', '🚀', '💎', '🧩', '⚡', '🔧', '📱', '🖥️', '🎯', '🛍️', '💡', '📊'];
        @endphp
        @for ($i = 0; $i < 22; $i++)
            <span class="auth-fall-item"
                  style="left: {{ random_int(0, 98) }}%;
                         font-size: {{ random_int(18, 40) }}px;
                         animation-duration: {{ random_int(9, 20) }}s;
                         animation-delay: -{{ random_int(0, 18) }}s;">{{ $authGlyphs[array_rand($authGlyphs)] }}</span>
        @endfor
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
