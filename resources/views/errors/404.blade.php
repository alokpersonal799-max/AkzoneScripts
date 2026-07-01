<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head')
    <title>404 — Page not found · {{ setting('site_name', config('app.name')) }}</title>
    <style>
        @keyframes err-float { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(28px,-22px) scale(1.12); } }
        .err-orb { position: absolute; border-radius: 9999px; filter: blur(80px); opacity: .45; }
    </style>
</head>
<body class="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-100 px-4 font-sans text-slate-600 antialiased">

    {{-- Decorative background --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="err-orb" style="width:460px;height:460px;top:-120px;left:-90px;background:#93bbfd;animation:err-float 20s ease-in-out infinite;"></div>
        <div class="err-orb" style="width:420px;height:420px;bottom:-120px;right:-80px;background:#c4b5fd;animation:err-float 26s ease-in-out infinite reverse;"></div>
    </div>

    <div class="relative w-full max-w-lg text-center">
        {{-- Big 404 --}}
        <p class="font-display text-[7rem] font-extrabold leading-none tracking-tight text-transparent sm:text-[9rem]"
           style="background:linear-gradient(135deg,#2563eb,#7c3aed);-webkit-background-clip:text;background-clip:text;">404</p>

        <h1 class="mt-2 font-display text-2xl font-extrabold text-ink-900 sm:text-3xl">Oops! Page not found</h1>
        <p class="mx-auto mt-3 max-w-md text-slate-500">
            The page you're looking for doesn't exist, was moved, or the link is broken. Let's get you back on track.
        </p>

        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ url('/') }}" class="btn-primary btn-lg">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                Back to home
            </a>
            <a href="{{ url('/products') }}" class="btn-ghost btn-lg border border-slate-200 bg-white">
                Browse marketplace
            </a>
        </div>

        {{-- Quick search --}}
        <form action="{{ url('/products') }}" method="GET" class="relative mx-auto mt-8 max-w-sm">
            <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
            <input type="text" name="q" placeholder="Search products..." class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-12 pr-4 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10">
        </form>

        <p class="mt-10 text-xs text-slate-400">&copy; {{ date('Y') }} {{ setting('site_name', config('app.name')) }}</p>
    </div>
</body>
</html>
