<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
    $metaTitle = isset($title) ? $title.' — '.setting('site_name', config('marketplace.name')) : (setting('seo_title') ?: setting('site_name', config('marketplace.name')));
    $metaDesc = setting('seo_description') ?: config('marketplace.tagline');
    $ogImage = setting('seo_og_image') ? \Illuminate\Support\Facades\Storage::disk('public')->url(setting('seo_og_image')) : null;
@endphp
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDesc }}">
@if (setting('seo_keywords'))<meta name="keywords" content="{{ setting('seo_keywords') }}">@endif

<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDesc }}">
<meta property="og:type" content="website">
@if ($ogImage)<meta property="og:image" content="{{ $ogImage }}"><meta name="twitter:card" content="summary_large_image">@endif

@if (setting('analytics_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ setting('analytics_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ setting('analytics_id') }}');
    </script>
@endif

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<script src="https://cdn.tailwindcss.com"></script>
@php
    $activeTheme = active_theme();
    $themeCfg = config('themes.'.$activeTheme) ?? config('themes.default');
    $brandPalette = $themeCfg['brand'] ?? config('themes.default.brand');
    $themeEffect = $themeCfg['effect'] ?? 'none';
@endphp
<script>document.documentElement.setAttribute('data-theme', @json($activeTheme));</script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    display: ['"Plus Jakarta Sans"', 'Inter', 'ui-sans-serif'],
                },
                colors: {
                    brand: {!! json_encode($brandPalette) !!},
                    ink: {
                        700: '#1b2540', 800: '#111a2e', 900: '#0b1120', 950: '#070b16',
                    },
                },
                boxShadow: {
                    soft: '0 1px 2px rgba(15,23,42,.04), 0 8px 24px -12px rgba(15,23,42,.12)',
                    lift: '0 12px 40px -12px rgba(37,99,235,.28)',
                },
                keyframes: {
                    floaty: { '0%,100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-12px)' } },
                    'fade-up': { '0%': { opacity: 0, transform: 'translateY(16px)' }, '100%': { opacity: 1, transform: 'none' } },
                },
                animation: {
                    floaty: 'floaty 6s ease-in-out infinite',
                    'fade-up': 'fade-up .6s cubic-bezier(.16,1,.3,1) both',
                },
            },
        },
    };
</script>

<style type="text/tailwindcss">
    @layer components {
        .btn { @apply inline-flex items-center justify-center gap-2 rounded-xl font-semibold transition duration-200; }
        .btn-primary { @apply btn bg-brand-600 text-white shadow-sm hover:bg-brand-700 hover:shadow-lift active:scale-[.98]; }
        .btn-dark { @apply btn bg-ink-900 text-white hover:bg-ink-800 active:scale-[.98]; }
        .btn-ghost { @apply btn border border-slate-200 bg-white text-slate-700 hover:bg-slate-50; }
        .btn-sm { @apply px-4 py-2 text-sm; }
        .btn-md { @apply px-5 py-2.5 text-sm; }
        .btn-lg { @apply px-6 py-3; }
        .input { @apply w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 transition focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10; }
        .label { @apply mb-1.5 block text-sm font-medium text-slate-700; }
        .card { @apply rounded-2xl border border-slate-200 bg-white shadow-soft; }
        .chip { @apply inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold; }
        .section-eyebrow { @apply inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-600; }
        .section-title { @apply font-display text-2xl font-extrabold tracking-tight text-ink-900 sm:text-3xl; }
    }
    @layer utilities {
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .gradient-text { @apply bg-gradient-to-r from-brand-500 to-indigo-500 bg-clip-text text-transparent; }
    }
</style>

<style>
    [x-cloak] { display: none !important; }
    html { scroll-behavior: smooth; }
    body { -webkit-font-smoothing: antialiased; }
    .dot-grid { background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.10) 1px, transparent 0); background-size: 28px 28px; }
    .reveal { opacity: 0; transform: translateY(26px); transition: opacity .7s cubic-bezier(.16,1,.3,1), transform .7s cubic-bezier(.16,1,.3,1); }
    .reveal.in-view { opacity: 1; transform: none; }
    .reveal-delay-1 { transition-delay: .08s; }
    .reveal-delay-2 { transition-delay: .16s; }
    .reveal-delay-3 { transition-delay: .24s; }
    ::-webkit-scrollbar { width: 11px; height: 11px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; border: 3px solid #f1f5f9; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

@if ($activeTheme === 'festival')
<style>
    html[data-theme="festival"] body { background-color: #fdf4ff; }
    html[data-theme="festival"] .shadow-lift { box-shadow: 0 12px 40px -12px rgba(192,38,211,.30) !important; }
</style>
@elseif ($activeTheme === 'prime')
<style>
    html[data-theme="prime"] body { background-color: #fff7ed; }
    html[data-theme="prime"] .shadow-lift { box-shadow: 0 12px 40px -12px rgba(234,88,12,.30) !important; }
</style>
@elseif ($activeTheme === 'christmas')
<style>
    html[data-theme="christmas"] body { background-color: #fef2f2; }
    html[data-theme="christmas"] .shadow-lift { box-shadow: 0 12px 40px -12px rgba(220,38,38,.30) !important; }
</style>
@elseif ($activeTheme === 'valentine')
<style>
    html[data-theme="valentine"] body { background-color: #fdf2f8; }
    html[data-theme="valentine"] .shadow-lift { box-shadow: 0 12px 40px -12px rgba(219,39,119,.30) !important; }
</style>
@elseif ($activeTheme === 'emerald')
<style> html[data-theme="emerald"] body { background-color: #f0fdf4; } </style>
@elseif ($activeTheme === 'ocean')
<style> html[data-theme="ocean"] body { background-color: #ecfeff; } </style>
@elseif ($activeTheme === 'sunset')
<style> html[data-theme="sunset"] body { background-color: #fffbeb; } </style>
@elseif ($activeTheme === 'midnight')
<style> html[data-theme="midnight"] body { background-color: #eef2ff; } </style>
@endif

{{-- Decorative theme effect animations (confetti / snow / hearts / ribbon) --}}
@if (in_array($themeEffect, ['confetti', 'snow', 'hearts', 'ribbon'], true))
<style>
    .theme-fx-item { position: absolute; top: -40px; will-change: transform; animation-name: theme-fall; animation-timing-function: linear; animation-iteration-count: infinite; }
    @keyframes theme-fall { 0% { transform: translateY(-40px) rotate(0deg); } 100% { transform: translateY(106vh) rotate(360deg); } }
    .theme-ribbon { position: absolute; top: 20px; right: -56px; transform: rotate(45deg); background: linear-gradient(90deg,#dc2626,#f97316); color: #fff; font-weight: 800; font-size: 12px; letter-spacing: 3px; padding: 7px 64px; box-shadow: 0 8px 20px rgba(0,0,0,.18); }
</style>
@endif

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    // Lightweight scroll-reveal animations.
    document.addEventListener('DOMContentLoaded', function () {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
    });
</script>
