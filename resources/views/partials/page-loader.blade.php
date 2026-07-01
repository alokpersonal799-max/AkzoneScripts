{{-- Full-screen page load animation. Themed with the active theme's brand colours. --}}
@php
    $ldTheme = config('themes.'.active_theme()) ?? config('themes.default');
    $ldBrand = $ldTheme['brand'] ?? config('themes.default.brand');
    $ld100 = $ldBrand['100'] ?? '#dbe8fe';
    $ld400 = $ldBrand['400'] ?? '#609afa';
    $ld500 = $ldBrand['500'] ?? '#3b82f6';
    $ld600 = $ldBrand['600'] ?? '#2563eb';
    $ld700 = $ldBrand['700'] ?? '#1d4ed8';
    $ldInitial = strtoupper(substr(setting('site_name', config('app.name', 'A')), 0, 1));
@endphp
<style>
    #akz-page-loader {
        position: fixed; inset: 0; z-index: 9999;
        display: flex; align-items: center; justify-content: center;
        background: #ffffff;
        transition: opacity .5s ease, visibility .5s ease;
    }
    #akz-page-loader.akz-hide { opacity: 0; visibility: hidden; }
    #akz-page-loader::before {
        content: ""; position: absolute; width: 260px; height: 260px; border-radius: 9999px;
        background: radial-gradient(circle, {{ $ld100 }} 0%, rgba(255,255,255,0) 70%);
        opacity: .8; animation: akz-glow 2.4s ease-in-out infinite;
    }
    .akz-l-wrap { position: relative; display: flex; flex-direction: column; align-items: center; gap: 22px; }
    .akz-l-orbit { position: relative; width: 84px; height: 84px; display: flex; align-items: center; justify-content: center; }
    .akz-l-ring {
        position: absolute; inset: 0; border-radius: 9999px;
        border: 4px solid {{ $ld100 }};
        border-top-color: {{ $ld600 }};
        border-right-color: {{ $ld400 }};
        animation: akz-spin .85s cubic-bezier(.6,.2,.4,.8) infinite;
    }
    .akz-l-ring2 {
        position: absolute; inset: 10px; border-radius: 9999px;
        border: 3px solid transparent;
        border-bottom-color: {{ $ld500 }};
        animation: akz-spin 1.3s linear infinite reverse;
    }
    .akz-l-badge {
        display: flex; align-items: center; justify-content: center;
        width: 48px; height: 48px; border-radius: 15px;
        background: linear-gradient(135deg, {{ $ld500 }}, {{ $ld700 }});
        color: #fff; font-weight: 800; font-size: 22px;
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        box-shadow: 0 10px 28px -8px {{ $ld600 }}88;
        animation: akz-pulse 1.4s ease-in-out infinite;
    }
    .akz-l-dots { display: flex; gap: 7px; }
    .akz-l-dots span { width: 8px; height: 8px; border-radius: 9999px; background: {{ $ld500 }}; animation: akz-bounce 1s ease-in-out infinite; }
    .akz-l-dots span:nth-child(2) { animation-delay: .15s; }
    .akz-l-dots span:nth-child(3) { animation-delay: .3s; }
    @keyframes akz-spin { to { transform: rotate(360deg); } }
    @keyframes akz-pulse { 0%,100% { transform: scale(1); } 50% { transform: scale(1.1); } }
    @keyframes akz-bounce { 0%,100% { transform: translateY(0); opacity: .5; } 50% { transform: translateY(-7px); opacity: 1; } }
    @keyframes akz-glow { 0%,100% { transform: scale(1); opacity: .55; } 50% { transform: scale(1.15); opacity: .9; } }
    @media (prefers-reduced-motion: reduce) {
        .akz-l-ring, .akz-l-ring2, .akz-l-badge, .akz-l-dots span, #akz-page-loader::before { animation: none; }
    }
</style>

<div id="akz-page-loader" aria-hidden="true">
    <div class="akz-l-wrap">
        <div class="akz-l-orbit">
            <div class="akz-l-ring"></div>
            <div class="akz-l-ring2"></div>
            <div class="akz-l-badge">{{ $ldInitial }}</div>
        </div>
        <div class="akz-l-dots"><span></span><span></span><span></span></div>
    </div>
</div>

<script>
    (function () {
        function hideLoader() {
            var el = document.getElementById('akz-page-loader');
            if (el) {
                el.classList.add('akz-hide');
                setTimeout(function () { if (el && el.parentNode) el.parentNode.removeChild(el); }, 550);
            }
        }
        if (document.readyState === 'complete') { hideLoader(); }
        else { window.addEventListener('load', hideLoader); setTimeout(hideLoader, 4000); }
        window.addEventListener('pageshow', function (e) { if (e.persisted) hideLoader(); });
    })();
</script>
