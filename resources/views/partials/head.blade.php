<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ isset($title) ? $title.' — '.config('marketplace.name') : config('marketplace.name') }}</title>
<meta name="description" content="{{ config('marketplace.tagline') }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">

<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    display: ['Sora', 'Inter', 'ui-sans-serif'],
                },
                colors: {
                    brand: {
                        50: '#ecfeff', 100: '#cffafe', 200: '#a5f3fc', 300: '#67e8f9',
                        400: '#22d3ee', 500: '#06b6d4', 600: '#0891b2', 700: '#0e7490',
                        800: '#155e75', 900: '#164e63',
                    },
                    ink: {
                        700: '#1e293b', 800: '#0f172a', 900: '#020617',
                    },
                },
                boxShadow: {
                    glow: '0 0 40px -10px rgba(34, 211, 238, 0.5)',
                },
            },
        },
    };
</script>
<style>
    [x-cloak] { display: none !important; }
    .gradient-text {
        background: linear-gradient(135deg, #22d3ee 0%, #818cf8 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .hero-grid {
        background-image: radial-gradient(circle at 1px 1px, rgba(148,163,184,0.15) 1px, transparent 0);
        background-size: 32px 32px;
    }
    ::-webkit-scrollbar { width: 10px; height: 10px; }
    ::-webkit-scrollbar-track { background: #0f172a; }
    ::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }
    ::-webkit-scrollbar-thumb:hover { background: #475569; }
</style>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
