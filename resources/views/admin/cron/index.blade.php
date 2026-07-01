@extends('layouts.admin')

@section('page-title', 'Cron Jobs')

@section('admin')
<div class="mx-auto max-w-4xl space-y-8">
    <div>
        <h1 class="font-display text-2xl font-extrabold text-ink-900">Cron Jobs</h1>
        <p class="mt-1 text-sm text-slate-500">Set up a single scheduled task on your server. Laravel's scheduler then runs every automated job (Telegram auto-promotion, daily reports, etc.) at the right time.</p>
    </div>

    {{-- The cron command --}}
    <div class="card p-6" x-data="{ copied: false, copy(t){ navigator.clipboard.writeText(t).then(() => { this.copied = true; setTimeout(() => this.copied = false, 2000); }); } }">
        <h2 class="font-display text-lg font-bold text-ink-900">1. Add this cron entry</h2>
        <p class="mt-1 text-sm text-slate-500">Add it to your hosting control panel (cPanel → Cron Jobs, Hostinger → Cron Jobs) or your server crontab. Set it to run <strong>every minute</strong>.</p>

        <div class="mt-4">
            <label class="label">Recommended (auto-detected paths)</label>
            <div class="flex items-stretch gap-2">
                <code class="flex-1 overflow-x-auto rounded-xl bg-ink-900 px-4 py-3 font-mono text-xs text-emerald-300">{{ $command }}</code>
                <button type="button" @click="copy(@js($command))" class="btn-primary btn-md flex-shrink-0">
                    <span x-show="!copied">Copy</span>
                    <span x-show="copied" x-cloak>Copied!</span>
                </button>
            </div>
        </div>

        <div class="mt-4">
            <label class="label">Simple version (if your host uses a global <code>php</code>)</label>
            <div class="flex items-stretch gap-2">
                <code class="flex-1 overflow-x-auto rounded-xl bg-slate-100 px-4 py-3 font-mono text-xs text-slate-700">{{ $simpleCommand }}</code>
                <button type="button" @click="copy(@js($simpleCommand))" class="btn-ghost btn-md flex-shrink-0">Copy</button>
            </div>
        </div>

        <dl class="mt-5 grid gap-3 rounded-xl bg-slate-50 p-4 text-xs sm:grid-cols-2">
            <div><dt class="font-semibold text-slate-500">Project path</dt><dd class="mt-0.5 break-all font-mono text-ink-900">{{ $basePath }}</dd></div>
            <div><dt class="font-semibold text-slate-500">PHP binary</dt><dd class="mt-0.5 break-all font-mono text-ink-900">{{ $phpBinary }}</dd></div>
        </dl>
    </div>

    {{-- How to (per host) --}}
    <div class="card p-6">
        <h2 class="font-display text-lg font-bold text-ink-900">2. Where to paste it</h2>
        <ul class="mt-3 space-y-2 text-sm text-slate-600">
            <li class="flex gap-2"><span class="font-semibold text-ink-900">cPanel:</span> Cron Jobs → Common Settings: "Once Per Minute (* * * * *)" → paste the command.</li>
            <li class="flex gap-2"><span class="font-semibold text-ink-900">Hostinger:</span> Advanced → Cron Jobs → Create → interval every minute → paste the command.</li>
            <li class="flex gap-2"><span class="font-semibold text-ink-900">VPS / SSH:</span> run <code class="rounded bg-slate-100 px-1 font-mono">crontab -e</code> and add the line, then save.</li>
        </ul>
        <div class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-xs text-amber-700">
            You only need <strong>one</strong> cron entry — Laravel's scheduler decides what runs and when. Without it, some features still work via site traffic (see below), but timed jobs (daily report, precise auto-promo) need this entry.
        </div>
    </div>

    {{-- Scheduled tasks --}}
    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-5">
            <h2 class="font-display text-lg font-bold text-ink-900">3. Scheduled tasks &amp; where they're used</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach ($tasks as $task)
                <div class="p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-ink-900">{{ $task['name'] }}</p>
                            <p class="mt-0.5 text-xs text-slate-400">{{ $task['schedule'] }}</p>
                        </div>
                        @if ($task['enabled'])
                            <span class="chip bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">Enabled</span>
                        @else
                            <span class="chip bg-slate-100 text-slate-500">Disabled</span>
                        @endif
                    </div>
                    <p class="mt-2 text-sm text-slate-600">{{ $task['purpose'] }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400">
                        <span>⚙️ Configure: <span class="font-medium text-slate-600">{{ $task['where'] }}</span></span>
                        @if (! empty($task['last']))
                            <span>🕒 Last run: <span class="font-medium text-slate-600">{{ \Illuminate\Support\Carbon::parse($task['last'])->diffForHumans() }}</span></span>
                        @else
                            <span>🕒 Last run: <span class="font-medium text-slate-600">never</span></span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Works without cron --}}
    <div class="card p-6">
        <h2 class="font-display text-lg font-bold text-ink-900">Also runs without cron</h2>
        <p class="mt-1 text-sm text-slate-500">These features are triggered by normal site activity, so they work even if you never set up cron:</p>
        <ul class="mt-3 space-y-2">
            @foreach ($noCronFeatures as $f)
                <li class="flex items-start gap-2 text-sm">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    <span><span class="font-semibold text-ink-900">{{ $f['name'] }}</span> — <span class="text-slate-500">{{ $f['note'] }}</span></span>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
