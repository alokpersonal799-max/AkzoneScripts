@extends('layouts.install')

@section('install')
    <h1 class="font-display text-xl font-bold text-ink-900">Choose your setup mode</h1>
    <p class="mt-1 text-sm text-slate-500">The connection works! Now pick how you'd like to start. You can switch later from the admin dashboard.</p>

    <form method="POST" action="{{ route('install.import.run') }}" class="mt-6"
          x-data="{ mode: 'demo', loading: false }" @submit="loading = true">
        @csrf
        <input type="hidden" name="demo" :value="mode === 'demo' ? '1' : '0'">

        <div class="grid gap-4 sm:grid-cols-2">
            {{-- Demo mode --}}
            <label class="relative cursor-pointer rounded-2xl border-2 p-5 transition"
                   :class="mode === 'demo' ? 'border-brand-500 bg-brand-50/60 ring-2 ring-brand-500/20' : 'border-slate-200 hover:border-slate-300'">
                <input type="radio" name="mode_choice" value="demo" x-model="mode" class="sr-only">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-100 text-brand-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                    </span>
                    <span class="flex h-5 w-5 items-center justify-center rounded-full border-2" :class="mode === 'demo' ? 'border-brand-500 bg-brand-500' : 'border-slate-300'">
                        <svg x-show="mode === 'demo'" class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke-width="4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    </span>
                </div>
                <h3 class="mt-3 font-display text-base font-bold text-ink-900">Take a demo first</h3>
                <p class="mt-1 text-xs text-slate-500">Loads sample products, gallery images, ratings and demo accounts so you can explore everything instantly.</p>
                <span class="mt-3 inline-block rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">Recommended to explore</span>
            </label>

            {{-- Business mode --}}
            <label class="relative cursor-pointer rounded-2xl border-2 p-5 transition"
                   :class="mode === 'business' ? 'border-brand-500 bg-brand-50/60 ring-2 ring-brand-500/20' : 'border-slate-200 hover:border-slate-300'">
                <input type="radio" name="mode_choice" value="business" x-model="mode" class="sr-only">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" /></svg>
                    </span>
                    <span class="flex h-5 w-5 items-center justify-center rounded-full border-2" :class="mode === 'business' ? 'border-brand-500 bg-brand-500' : 'border-slate-300'">
                        <svg x-show="mode === 'business'" x-cloak class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke-width="4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    </span>
                </div>
                <h3 class="mt-3 font-display text-base font-bold text-ink-900">Ready for business</h3>
                <p class="mt-1 text-xs text-slate-500">A completely fresh, empty store. You'll set your brand name and create your own admin login next.</p>
                <span class="mt-3 inline-block rounded-full bg-indigo-100 px-2.5 py-0.5 text-[11px] font-bold text-indigo-700">Go live directly</span>
            </label>
        </div>

        {{-- Demo details --}}
        <div x-show="mode === 'demo'" x-transition class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <p class="font-semibold">A demo admin login is created automatically:</p>
            <p class="mt-1">Admin &mdash; <code class="rounded bg-white/70 px-1 font-mono">admin@akzone.com</code> / <code class="rounded bg-white/70 px-1 font-mono">password</code></p>
            <p class="mt-0.5">Customer &mdash; <code class="rounded bg-white/70 px-1 font-mono">user@akzone.com</code> / <code class="rounded bg-white/70 px-1 font-mono">password</code></p>
            <p class="mt-2 text-xs text-emerald-700">When you're ready for business, remove all sample content in one click from <strong>Admin&nbsp;&rarr;&nbsp;Dashboard&nbsp;&rarr;&nbsp;Clear demo data</strong>. You'll skip creating an admin and go straight to finish.</p>
        </div>

        {{-- Business details --}}
        <div x-show="mode === 'business'" x-cloak x-transition class="mt-5 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
            <p class="font-semibold text-ink-900">Fresh business install</p>
            <p class="mt-1 text-xs">No sample products, reviews or accounts. Next step: enter your <strong>brand name</strong> and create your own <strong>admin email &amp; password</strong>. Product ratings will build up from real verified buyers.</p>
        </div>

        <div class="mt-8 flex items-center justify-between">
            <a href="{{ route('install.database') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back</a>
            <button type="submit" :disabled="loading" class="btn-primary btn-lg disabled:opacity-60">
                <svg x-show="loading" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path></svg>
                <span x-text="loading ? 'Setting up…' : (mode === 'demo' ? 'Install demo & finish' : 'Continue to admin setup')">Continue</span>
            </button>
        </div>
    </form>
@endsection
