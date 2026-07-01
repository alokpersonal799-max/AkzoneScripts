{{-- Full-screen page load animation. Fades out once the page has loaded. --}}
<style>
    #akz-page-loader {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        transition: opacity .45s ease, visibility .45s ease;
    }
    #akz-page-loader.akz-hide { opacity: 0; visibility: hidden; }
    .akz-loader-box { position: relative; display: flex; align-items: center; justify-content: center; }
    .akz-loader-ring {
        position: absolute;
        width: 72px; height: 72px;
        border-radius: 9999px;
        border: 3px solid #e2e8f0;
        border-top-color: #2563eb;
        border-right-color: #6366f1;
        animation: akz-spin .8s linear infinite;
    }
    .akz-loader-badge {
        display: flex; align-items: center; justify-content: center;
        width: 48px; height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #2563eb, #6366f1);
        color: #fff;
        font-weight: 800;
        font-size: 22px;
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        box-shadow: 0 10px 30px -8px rgba(37, 99, 235, .5);
        animation: akz-pulse 1.4s ease-in-out infinite;
    }
    @keyframes akz-spin { to { transform: rotate(360deg); } }
    @keyframes akz-pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.08); } }
    @media (prefers-reduced-motion: reduce) {
        .akz-loader-ring, .akz-loader-badge { animation: none; }
    }
</style>

<div id="akz-page-loader" aria-hidden="true">
    <div class="akz-loader-box">
        <div class="akz-loader-ring"></div>
        <div class="akz-loader-badge">{{ strtoupper(substr(setting('site_name', config('app.name', 'A')), 0, 1)) }}</div>
    </div>
</div>

<script>
    (function () {
        function hideLoader() {
            var el = document.getElementById('akz-page-loader');
            if (el) {
                el.classList.add('akz-hide');
                setTimeout(function () { if (el && el.parentNode) el.parentNode.removeChild(el); }, 500);
            }
        }
        if (document.readyState === 'complete') {
            hideLoader();
        } else {
            window.addEventListener('load', hideLoader);
            // Safety net: never keep the overlay longer than 4s.
            setTimeout(hideLoader, 4000);
        }
        // Show again briefly when navigating away (nice transition out).
        window.addEventListener('pageshow', function (e) { if (e.persisted) hideLoader(); });
    })();
</script>
