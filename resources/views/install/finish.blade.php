@extends('layouts.install')

@section('install')
    <div class="text-center">
        <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-500">
            <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </span>
        <h1 class="mt-5 font-display text-2xl font-extrabold text-ink-900">
            @if ($demo) Demo is ready! 🎉 @else You're ready for business! 🚀 @endif
        </h1>
        <p class="mt-2 text-slate-500">
            @if ($demo)
                Your store is loaded with sample content so you can explore every feature. The installer is now locked for security.
            @else
                Your fresh store is set up with your own admin account and no demo data. The installer is now locked for security.
            @endif
        </p>

        @if ($demo)
            {{-- Demo install: show the auto-created login so they can sign in immediately --}}
            <div class="mt-7 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-left text-sm text-emerald-800">
                <p class="font-bold">Your demo admin login</p>
                <div class="mt-2 grid gap-2 sm:grid-cols-2">
                    <div class="rounded-lg bg-white/70 px-3 py-2">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-600">Admin</p>
                        <p class="font-mono text-xs">{{ $demoEmail }}</p>
                        <p class="font-mono text-xs">{{ $demoPassword }}</p>
                    </div>
                    <div class="rounded-lg bg-white/70 px-3 py-2">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-600">Customer</p>
                        <p class="font-mono text-xs">user@akzone.com</p>
                        <p class="font-mono text-xs">password</p>
                    </div>
                </div>
            </div>

            {{-- How to leave demo mode when ready --}}
            <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-left text-sm text-amber-800">
                <p class="font-bold">When you're ready for business</p>
                <p class="mt-1">You don't need to reinstall. Just log in to the admin dashboard and click <strong>&ldquo;Clear demo data&rdquo;</strong> at the top of the dashboard — it removes all sample products, reviews and demo accounts in one click, leaving a clean store. Then change your admin password in Settings.</p>
            </div>
        @else
            <div class="mt-7 rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-left text-sm text-slate-600">
                <p class="font-bold text-ink-900">Next steps</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    <li>Log in to the admin panel with the email &amp; password you just created.</li>
                    <li>Add your categories and products, then upload their files (ZIP packages).</li>
                    <li>Configure payments, storage and branding from Admin &rarr; Settings.</li>
                </ul>
            </div>
        @endif

        <div class="mt-7 grid gap-3 sm:grid-cols-2">
            <a href="{{ rtrim($appUrl, '/') }}/admin" class="btn-primary btn-lg">Login to admin dashboard</a>
            <a href="{{ $appUrl }}" class="btn-ghost btn-lg border border-slate-200">Visit website</a>
        </div>

        <p class="mt-5 text-xs text-slate-400">The installer is locked by the <code>storage/installed</code> file. Delete that file only if you need to re-run setup. See the <a href="{{ route('install.manual') }}" class="font-semibold text-slate-500 hover:text-brand-600">Setup Manual</a> for full details.</p>
    </div>
@endsection
