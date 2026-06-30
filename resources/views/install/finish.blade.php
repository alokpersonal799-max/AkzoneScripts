@extends('layouts.install')

@section('install')
    <div class="text-center">
        <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-500/15 text-emerald-400">
            <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </span>
        <h1 class="mt-5 font-display text-2xl font-bold text-white">Installation complete! 🎉</h1>
        <p class="mt-2 text-slate-400">Your marketplace is ready. The installer is now locked for security.</p>

        <div class="mt-8 grid gap-3 sm:grid-cols-2">
            <a href="{{ $appUrl }}" class="rounded-xl bg-brand-400 px-6 py-3 font-semibold text-ink-900 transition hover:bg-brand-300">Visit storefront</a>
            <a href="{{ rtrim($appUrl, '/') }}/admin" class="rounded-xl border border-white/10 px-6 py-3 font-semibold text-white transition hover:bg-white/5">Go to admin panel</a>
        </div>

        <div class="mt-8 rounded-xl border border-amber-500/20 bg-amber-500/5 px-4 py-4 text-left text-sm text-amber-200">
            <p class="font-semibold">Recommended next steps</p>
            <ul class="mt-2 list-inside list-disc space-y-1 text-amber-200/90">
                <li>Log in to <code>/admin</code> with the email &amp; password you just created.</li>
                <li>Upload product files (ZIP packages) so customers can download them.</li>
                <li>For security you can delete the <code>app/Http/Controllers/InstallController.php</code> if you wish — the installer is already locked by the <code>storage/installed</code> file.</li>
            </ul>
        </div>
    </div>
@endsection
