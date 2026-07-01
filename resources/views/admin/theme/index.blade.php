@extends('layouts.admin')

@section('page-title', 'Theme')

@section('admin')
<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <h1 class="font-display text-2xl font-extrabold text-ink-900">Store Theme</h1>
        <p class="mt-1 text-sm text-slate-500">Pick a look for your whole storefront. Switching a theme instantly re-skins colors, buttons, and accents everywhere.</p>
    </div>

    <form method="POST" action="{{ route('admin.theme.update') }}" x-data="{ picked: '{{ old('active_theme', $active) }}' }">
        @csrf
        @method('PUT')

        <div class="grid gap-5 sm:grid-cols-3">
            @foreach ($themes as $key => $theme)
                <label class="relative block cursor-pointer">
                    <input type="radio" name="active_theme" value="{{ $key }}" x-model="picked" class="peer sr-only">
                    <div class="rounded-2xl border-2 p-5 transition"
                         :class="picked === '{{ $key }}' ? 'border-brand-500 bg-brand-50/40 shadow-soft' : 'border-slate-200 bg-white hover:border-slate-300'">
                        {{-- Swatches --}}
                        <div class="flex items-center gap-2">
                            @foreach ($theme['colors'] as $c)
                                <span class="h-8 w-8 rounded-lg shadow-sm ring-1 ring-black/5" style="background: {{ $c }}"></span>
                            @endforeach
                            <span x-show="picked === '{{ $key }}'" x-cloak class="ml-auto flex h-6 w-6 items-center justify-center rounded-full bg-brand-500 text-white">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            </span>
                        </div>
                        <h3 class="mt-4 font-display text-lg font-bold text-ink-900">{{ $theme['label'] }}</h3>
                        <p class="mt-1 text-xs leading-relaxed text-slate-500">{{ $theme['desc'] }}</p>

                        {{-- Mini preview --}}
                        <div class="mt-4 space-y-2 rounded-xl border border-slate-100 bg-white p-3">
                            <div class="h-2 w-2/3 rounded-full" style="background: {{ $theme['colors'][1] }}"></div>
                            <div class="h-2 w-full rounded-full bg-slate-100"></div>
                            <div class="mt-2 inline-flex rounded-lg px-3 py-1.5 text-[10px] font-bold text-white" style="background: {{ $theme['colors'][0] }}">Buy now</div>
                        </div>
                    </div>
                </label>
            @endforeach
        </div>

        <div class="mt-6 flex items-center justify-between rounded-2xl bg-slate-50 px-5 py-4">
            <p class="text-sm text-slate-500">Currently active: <span class="font-semibold text-ink-900">{{ $themes[$active]['label'] ?? 'Default' }}</span></p>
            <button type="submit" class="btn-primary btn-lg">Apply theme</button>
        </div>
    </form>
</div>
@endsection
