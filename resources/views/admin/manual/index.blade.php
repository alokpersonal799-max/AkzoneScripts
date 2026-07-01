@extends('layouts.admin')

@section('page-title', 'Setup Manual')

@section('admin')
<div class="mx-auto max-w-4xl space-y-8"
     x-data="{ copied: null, copy(id, t){ navigator.clipboard.writeText(t).then(() => { this.copied = id; setTimeout(() => this.copied = null, 2000); }); } }">

    <div>
        <h1 class="font-display text-2xl font-extrabold text-ink-900">Setup Manual</h1>
        <p class="mt-1 text-sm text-slate-500">A step-by-step guide to install and launch this PHP script for your business. Follow the steps in order — for a real store, <strong>skip the demo data</strong> so you start with a clean, empty catalog.</p>
    </div>

    {{-- Requirements --}}
    <div class="card p-6">
        <h2 class="font-display text-lg font-bold text-ink-900">Server requirements</h2>
        <ul class="mt-3 grid gap-2 text-sm text-slate-600 sm:grid-cols-2">
            <li class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> PHP <strong>8.2</strong> or higher</li>
            <li class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> MySQL 5.7+ / MariaDB 10.3+</li>
            <li class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> Composer (for manual installs)</li>
            <li class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> Apache/Nginx with mod_rewrite</li>
            <li class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> PHP extensions: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo, GD</li>
            <li class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> SSL certificate (HTTPS) for live payments</li>
        </ul>
    </div>

    {{-- Step 1 --}}
    <div class="card p-6">
        <h2 class="font-display text-lg font-bold text-ink-900"><span class="text-brand-600">Step 1.</span> Upload the files</h2>
        <p class="mt-1 text-sm text-slate-500">Upload the script archive to your hosting and extract it. Your web server's domain/subdomain <strong>document root must point to the <code class="rounded bg-slate-100 px-1 font-mono">/public</code> folder</strong> (not the project root) so your <code class="rounded bg-slate-100 px-1 font-mono">.env</code> and source stay private.</p>
        <ul class="mt-3 space-y-2 text-sm text-slate-600">
            <li class="flex gap-2"><span class="font-semibold text-ink-900">Shared / cPanel:</span> extract into your domain folder, then set the domain's "Document Root" to <code class="rounded bg-slate-100 px-1 font-mono">.../public</code>.</li>
            <li class="flex gap-2"><span class="font-semibold text-ink-900">Local (XAMPP):</span> place in <code class="rounded bg-slate-100 px-1 font-mono">htdocs/</code> and open <code class="rounded bg-slate-100 px-1 font-mono">http://localhost/AkzoneScripts/public/</code>.</li>
        </ul>
    </div>

    {{-- Step 2 - Web installer --}}
    <div class="card p-6">
        <h2 class="font-display text-lg font-bold text-ink-900"><span class="text-brand-600">Step 2.</span> Run the web installer <span class="chip ml-1 bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">Recommended</span></h2>
        <p class="mt-1 text-sm text-slate-500">Open your site in a browser — you'll be redirected to the installer automatically. Complete the guided steps:</p>
        <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm text-slate-600">
            <li><strong>Requirements check</strong> — confirm all green ticks.</li>
            <li><strong>Database</strong> — enter your MySQL host, database name, username and password. The installer writes your <code class="rounded bg-slate-100 px-1 font-mono">.env</code> and runs migrations.</li>
            <li><strong>Site &amp; Admin</strong> — set your store name and create your admin account (this becomes your real login).</li>
            <li><strong>Demo data</strong> — for a live business, <strong>leave this OFF</strong>. Your store starts empty with 0 products and 0 reviews, ready for your real catalog. Turn it on only if you want sample content to explore first.</li>
            <li><strong>Finish</strong> — you'll be sent to the admin login.</li>
        </ol>
        <div class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-xs text-amber-700">
            The installer locks itself after completion. To re-run it, delete <code class="rounded bg-amber-100 px-1 font-mono">storage/installed</code>.
        </div>
    </div>

    {{-- Step 3 - Manual install (alternative) --}}
    <div class="card p-6">
        <h2 class="font-display text-lg font-bold text-ink-900"><span class="text-slate-400">Alternative.</span> Manual install via SSH</h2>
        <p class="mt-1 text-sm text-slate-500">Prefer the command line? Run these from the project root instead of the web installer.</p>
        @php
            $manualCmds = "composer install --no-dev --optimize-autoloader\ncp .env.example .env   # if .env is missing\nphp artisan key:generate\nphp artisan migrate\nphp artisan storage:link\nphp artisan optimize:clear";
        @endphp
        <div class="mt-3 flex items-stretch gap-2">
            <pre class="flex-1 overflow-x-auto rounded-xl bg-ink-900 px-4 py-3 font-mono text-xs leading-relaxed text-emerald-300">{{ $manualCmds }}</pre>
            <button type="button" @click="copy('manual', @js($manualCmds))" class="btn-primary btn-md flex-shrink-0 self-start">
                <span x-show="copied !== 'manual'">Copy</span>
                <span x-show="copied === 'manual'" x-cloak>Copied!</span>
            </button>
        </div>
        <p class="mt-3 text-xs text-slate-500">Then set your database credentials in <code class="rounded bg-slate-100 px-1 font-mono">.env</code> (<code class="rounded bg-slate-100 px-1 font-mono">DB_DATABASE</code>, <code class="rounded bg-slate-100 px-1 font-mono">DB_USERNAME</code>, <code class="rounded bg-slate-100 px-1 font-mono">DB_PASSWORD</code>) and re-run <code class="rounded bg-slate-100 px-1 font-mono">php artisan migrate</code>.</p>
        <p class="mt-2 text-xs text-slate-500"><strong>On Windows PowerShell:</strong> run each command on its own line (<code class="rounded bg-slate-100 px-1 font-mono">&amp;&amp;</code> is not supported). Use <code class="rounded bg-slate-100 px-1 font-mono">copy .env.example .env</code> instead of <code class="rounded bg-slate-100 px-1 font-mono">cp</code>.</p>
    </div>

    {{-- Step 4 - Cron --}}
    <div class="card p-6">
        <h2 class="font-display text-lg font-bold text-ink-900"><span class="text-brand-600">Step 3.</span> Add the scheduler cron</h2>
        <p class="mt-1 text-sm text-slate-500">Needed for timed jobs (Telegram daily report, precise auto-promotion, scheduled themes). Add this <strong>one</strong> entry to your host's Cron Jobs, set to run every minute:</p>
        <div class="mt-3 flex items-stretch gap-2">
            <code class="flex-1 overflow-x-auto rounded-xl bg-ink-900 px-4 py-3 font-mono text-xs text-emerald-300">{{ $cronCommand }}</code>
            <button type="button" @click="copy('cron', @js($cronCommand))" class="btn-primary btn-md flex-shrink-0">
                <span x-show="copied !== 'cron'">Copy</span>
                <span x-show="copied === 'cron'" x-cloak>Copied!</span>
            </button>
        </div>
        <p class="mt-3 text-xs text-slate-500">Full details and per-host instructions are on the <a href="{{ route('admin.cron.index') }}" class="font-semibold text-brand-600 hover:underline">Cron Jobs</a> page. Without cron, most features still work via normal site traffic.</p>
    </div>

    {{-- Step 5 - Configure store --}}
    <div class="card p-6">
        <h2 class="font-display text-lg font-bold text-ink-900"><span class="text-brand-600">Step 4.</span> Configure your store</h2>
        <p class="mt-1 text-sm text-slate-500">Set these up from the admin panel before you launch:</p>
        <div class="mt-3 grid gap-3 sm:grid-cols-2">
            <a href="{{ route('admin.settings.index') }}" class="rounded-xl border border-slate-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/40">
                <p class="font-semibold text-ink-900">General &amp; Branding</p>
                <p class="mt-0.5 text-xs text-slate-500">Store name, logo, contact details, currency, PWA install button.</p>
            </a>
            <a href="{{ route('admin.settings.show', 'payments') }}" class="rounded-xl border border-slate-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/40">
                <p class="font-semibold text-ink-900">Payment methods</p>
                <p class="mt-0.5 text-xs text-slate-500">Enable Stripe/PayPal/Razorpay keys, or manual UPI / Bank / Crypto with QR icons.</p>
            </a>
            <a href="{{ route('admin.storage.index') }}" class="rounded-xl border border-slate-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/40">
                <p class="font-semibold text-ink-900">File storage</p>
                <p class="mt-0.5 text-xs text-slate-500">Keep local, or connect S3 / Spaces / R2 and test the connection.</p>
            </a>
            <a href="{{ route('admin.products.index') }}" class="rounded-xl border border-slate-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/40">
                <p class="font-semibold text-ink-900">Add your products</p>
                <p class="mt-0.5 text-xs text-slate-500">Create categories, then add products with thumbnails, gallery and files.</p>
            </a>
        </div>
    </div>

    {{-- Step 6 - Go live checklist --}}
    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-5">
            <h2 class="font-display text-lg font-bold text-ink-900"><span class="text-brand-600">Step 5.</span> Go-live checklist</h2>
            <p class="mt-1 text-sm text-slate-500">Final checks before you accept real customers.</p>
        </div>
        <ul class="divide-y divide-slate-100">
            @foreach ([
                ['t' => 'Change the admin password', 'd' => 'If you imported demo data, the default admin@akzone.com / password login is public. Change it (or delete demo accounts) immediately.'],
                ['t' => 'Do not import demo data on the live store', 'd' => 'A fresh install with demo data OFF has no sample products, users or reviews — ratings build up only from real verified buyers.'],
                ['t' => 'Set production mode in .env', 'd' => 'APP_ENV=production and APP_DEBUG=false so errors are never shown to visitors. Set APP_URL to your real https:// domain.'],
                ['t' => 'Enable HTTPS / SSL', 'd' => 'Required for secure checkout and card gateways. Force https on your domain.'],
                ['t' => 'Set up outgoing email (SMTP)', 'd' => 'Configure MAIL_* in .env so order receipts, password resets and notifications are delivered.'],
                ['t' => 'Switch payment gateways to live keys', 'd' => 'Replace test/sandbox keys with live keys in Settings → Payments and do one real test purchase.'],
                ['t' => 'Add the cron entry', 'd' => 'For daily reports and timed automation (Step 3).'],
                ['t' => 'Cache for speed', 'd' => 'Run php artisan config:cache and route:cache on production for faster page loads.'],
            ] as $c)
                <li class="flex items-start gap-3 p-4">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    <div>
                        <p class="text-sm font-semibold text-ink-900">{{ $c['t'] }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $c['d'] }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Support note --}}
    <div class="rounded-2xl bg-gradient-to-br from-brand-600 to-indigo-600 p-6 text-white">
        <h2 class="font-display text-lg font-bold">You're ready to launch! &#128640;</h2>
        <p class="mt-1 text-sm text-white/80">Detected project path: <code class="rounded bg-white/15 px-1 font-mono text-white">{{ $basePath }}</code></p>
        <p class="mt-1 text-sm text-white/80">Once configured, share your store URL and start selling. Manage everything from this admin panel.</p>
    </div>
</div>
@endsection
