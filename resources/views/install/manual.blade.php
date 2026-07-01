@extends('layouts.install')

@section('install')
<div x-data="{ copied: null, copy(id, t){ navigator.clipboard.writeText(t).then(() => { this.copied = id; setTimeout(() => this.copied = null, 2000); }); } }">

    <div class="flex items-center justify-between">
        <h1 class="font-display text-2xl font-extrabold text-ink-900">Setup Manual</h1>
        <a href="{{ url()->previous() }}" class="text-sm font-semibold text-slate-500 hover:text-brand-600">&larr; Back</a>
    </div>
    <p class="mt-1 text-sm text-slate-500">Everything you need to install and launch this PHP script — for a demo trial or a real business. For a live store, choose <strong>Ready for business</strong> so you start with a clean, empty catalog.</p>

    {{-- Default demo credentials --}}
    <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
        <h2 class="flex items-center gap-2 text-sm font-bold text-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H9v1.5H7.5v1.5H6v1.5H2.25v-1.5a1.5 1.5 0 0 1 .44-1.06l5.42-5.42c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" /></svg>
            Default demo logins (only when demo data is installed)
        </h2>
        <div class="mt-3 grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl bg-white/70 px-4 py-3 text-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-600">Admin</p>
                <p class="mt-1">ID: <code class="rounded bg-emerald-100 px-1 font-mono">admin@akzone.com</code></p>
                <p>Password: <code class="rounded bg-emerald-100 px-1 font-mono">password</code></p>
            </div>
            <div class="rounded-xl bg-white/70 px-4 py-3 text-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-600">Demo customer</p>
                <p class="mt-1">ID: <code class="rounded bg-emerald-100 px-1 font-mono">user@akzone.com</code></p>
                <p>Password: <code class="rounded bg-emerald-100 px-1 font-mono">password</code></p>
            </div>
        </div>
        <p class="mt-3 text-xs text-emerald-700"><strong>Change these passwords</strong> before going live, or start in <strong>Ready for business</strong> mode to create your own admin with no demo accounts.</p>
    </div>

    {{-- Requirements --}}
    <div class="mt-6 rounded-2xl border border-slate-200 p-5">
        <h2 class="font-display text-base font-bold text-ink-900">Server requirements</h2>
        <ul class="mt-3 grid gap-2 text-sm text-slate-600 sm:grid-cols-2">
            <li class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> PHP <strong>8.2+</strong></li>
            <li class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> MySQL 5.7+ / MariaDB 10.3+</li>
            <li class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> Composer (manual installs)</li>
            <li class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> Apache/Nginx + mod_rewrite</li>
            <li class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> Extensions: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo, cURL</li>
            <li class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> SSL (HTTPS) for live payments</li>
        </ul>
    </div>

    {{-- Steps --}}
    <div class="mt-6 space-y-4">
        <div class="rounded-2xl border border-slate-200 p-5">
            <h2 class="font-display text-base font-bold text-ink-900"><span class="text-brand-600">Step 1.</span> Upload the files</h2>
            <p class="mt-1 text-sm text-slate-500">Extract the script on your host and point the domain's document root at the <code class="rounded bg-slate-100 px-1 font-mono">/public</code> folder so your <code class="rounded bg-slate-100 px-1 font-mono">.env</code> stays private. On XAMPP, place it in <code class="rounded bg-slate-100 px-1 font-mono">htdocs/</code> and open <code class="rounded bg-slate-100 px-1 font-mono">http://localhost/AkzoneScripts/public/</code>.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 p-5">
            <h2 class="font-display text-base font-bold text-ink-900"><span class="text-brand-600">Step 2.</span> Run this installer</h2>
            <p class="mt-1 text-sm text-slate-500">Open the site — the wizard runs automatically: requirements &rarr; permissions &rarr; database &rarr; setup mode. At the <strong>Setup Mode</strong> step you choose:</p>
            <ul class="mt-2 space-y-2 text-sm text-slate-600">
                <li class="rounded-xl bg-brand-50/60 px-4 py-2.5"><strong class="text-ink-900">Take a demo first</strong> — loads sample products &amp; reviews and creates the demo logins above. You go straight to finish. Clear it later from <strong>Admin &rarr; Dashboard &rarr; Clear demo data</strong>.</li>
                <li class="rounded-xl bg-indigo-50/60 px-4 py-2.5"><strong class="text-ink-900">Ready for business</strong> — a fresh, empty store. You then set your <strong>brand name</strong> and create your <strong>own admin email &amp; password</strong>. No demo data.</li>
            </ul>
        </div>

        <div class="rounded-2xl border border-slate-200 p-5">
            <h2 class="font-display text-base font-bold text-ink-900"><span class="text-slate-400">Alternative.</span> Manual install via SSH</h2>
            @php $manualCmds = "composer install --no-dev --optimize-autoloader\ncp .env.example .env\nphp artisan key:generate\nphp artisan migrate\nphp artisan storage:link\nphp artisan optimize:clear"; @endphp
            <div class="mt-3 flex items-stretch gap-2">
                <pre class="flex-1 overflow-x-auto rounded-xl bg-ink-900 px-4 py-3 font-mono text-xs leading-relaxed text-emerald-300">{{ $manualCmds }}</pre>
                <button type="button" @click="copy('m', @js($manualCmds))" class="btn-primary btn-md flex-shrink-0 self-start"><span x-show="copied !== 'm'">Copy</span><span x-show="copied === 'm'" x-cloak>Copied!</span></button>
            </div>
            <p class="mt-2 text-xs text-slate-500">Set <code class="rounded bg-slate-100 px-1 font-mono">DB_*</code> in <code class="rounded bg-slate-100 px-1 font-mono">.env</code> first. On Windows PowerShell run each line separately and use <code class="rounded bg-slate-100 px-1 font-mono">copy</code> instead of <code class="rounded bg-slate-100 px-1 font-mono">cp</code>.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 p-5">
            <h2 class="font-display text-base font-bold text-ink-900"><span class="text-brand-600">Step 3.</span> Add the scheduler cron</h2>
            <p class="mt-1 text-sm text-slate-500">For timed jobs (Telegram daily report, auto-promotion, scheduled themes). Add one entry set to run every minute:</p>
            <div class="mt-3 flex items-stretch gap-2">
                <code class="flex-1 overflow-x-auto rounded-xl bg-ink-900 px-4 py-3 font-mono text-xs text-emerald-300">{{ $cronCommand }}</code>
                <button type="button" @click="copy('c', @js($cronCommand))" class="btn-primary btn-md flex-shrink-0"><span x-show="copied !== 'c'">Copy</span><span x-show="copied === 'c'" x-cloak>Copied!</span></button>
            </div>
            <p class="mt-2 text-xs text-slate-500">Without cron, most features still run on normal site traffic.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 p-5">
            <h2 class="font-display text-base font-bold text-ink-900"><span class="text-brand-600">Step 4.</span> Configure your store</h2>
            <p class="mt-1 text-sm text-slate-500">From the admin panel: set branding &amp; currency (Settings &rarr; General), enable payment methods (Stripe/PayPal/Razorpay or manual UPI/Bank/Crypto), pick file storage (local or S3/Spaces/R2), then add your categories and products.</p>
        </div>
    </div>

    {{-- Go-live checklist --}}
    <div class="mt-6 rounded-2xl border border-slate-200 p-5">
        <h2 class="font-display text-base font-bold text-ink-900">Go-live checklist</h2>
        <ul class="mt-3 space-y-2 text-sm text-slate-600">
            @foreach ([
                'Change the admin password (or use Ready-for-business mode with no demo accounts).',
                'Do not install demo data on the live store — ratings then build from real verified buyers only.',
                'Set APP_ENV=production and APP_DEBUG=false in .env; set APP_URL to your https:// domain.',
                'Enable HTTPS/SSL and force it on your domain.',
                'Configure SMTP (MAIL_*) so receipts, resets and notifications are delivered.',
                'Switch payment gateways from test keys to live keys and do one real test purchase.',
                'Add the cron entry (Step 3) and run php artisan config:cache & route:cache for speed.',
            ] as $item)
                <li class="flex items-start gap-2">
                    <svg class="mt-0.5 h-4 w-4 flex-none text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    <span>{{ $item }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="mt-6 rounded-2xl bg-gradient-to-br from-brand-600 to-indigo-600 p-5 text-white">
        <p class="text-sm text-white/80">Detected project path: <code class="rounded bg-white/15 px-1 font-mono text-white">{{ $basePath }}</code></p>
        <p class="mt-1 text-sm text-white/80">Store URL: <code class="rounded bg-white/15 px-1 font-mono text-white">{{ $appUrl }}</code></p>
    </div>
</div>
@endsection
