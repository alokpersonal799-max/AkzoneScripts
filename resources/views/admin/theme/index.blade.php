@extends('layouts.admin')

@section('page-title', 'Theme')

@section('admin')
<div class="mx-auto max-w-5xl space-y-8">
    <div>
        <h1 class="font-display text-2xl font-extrabold text-ink-900">Store Theme</h1>
        <p class="mt-1 text-sm text-slate-500">Pick a look for your whole storefront. Switching a theme instantly re-skins colors, buttons and accents — some themes add festive effects like confetti, snow, hearts or a SALE ribbon.</p>
    </div>

    {{-- Theme picker --}}
    <form method="POST" action="{{ route('admin.theme.update') }}" x-data="{ picked: '{{ old('active_theme', $active) }}' }">
        @csrf
        @method('PUT')

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($themes as $key => $theme)
                <label class="relative block cursor-pointer">
                    <input type="radio" name="active_theme" value="{{ $key }}" x-model="picked" class="peer sr-only">
                    <div class="h-full rounded-2xl border-2 p-5 transition"
                         :class="picked === '{{ $key }}' ? 'border-brand-500 bg-brand-50/40 shadow-soft' : 'border-slate-200 bg-white hover:border-slate-300'">
                        <div class="flex items-center gap-2">
                            @foreach ($theme['swatch'] as $c)
                                <span class="h-8 w-8 rounded-lg shadow-sm ring-1 ring-black/5" style="background: {{ $c }}"></span>
                            @endforeach
                            <span x-show="picked === '{{ $key }}'" x-cloak class="ml-auto flex h-6 w-6 items-center justify-center rounded-full bg-brand-500 text-white">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            </span>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <h3 class="font-display text-lg font-bold text-ink-900">{{ $theme['label'] }}</h3>
                            @if (($theme['effect'] ?? 'none') !== 'none')
                                <span class="chip bg-slate-100 text-slate-500">{{ ucfirst($theme['effect']) }}</span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs leading-relaxed text-slate-500">{{ $theme['desc'] }}</p>
                        <div class="mt-4 space-y-2 rounded-xl border border-slate-100 bg-white p-3">
                            <div class="h-2 w-2/3 rounded-full" style="background: {{ $theme['swatch'][1] }}"></div>
                            <div class="h-2 w-full rounded-full bg-slate-100"></div>
                            <div class="mt-2 inline-flex rounded-lg px-3 py-1.5 text-[10px] font-bold text-white" style="background: {{ $theme['swatch'][0] }}">Buy now</div>
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

    {{-- Theme scheduling --}}
    <form method="POST" action="{{ route('admin.theme.schedule') }}" class="card p-6">
        @csrf
        @method('PUT')
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <h2 class="font-display text-lg font-bold text-ink-900">Schedule a theme</h2>
                <p class="mt-1 text-sm text-slate-500">Auto-activate a theme between two dates (e.g. Christmas or a sale week). Outside the window, your selected theme above is used.</p>
            </div>
            <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                <input type="checkbox" name="theme_schedule_enabled" value="1" {{ $schedule['enabled'] ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                Enabled
            </label>
        </div>

        <div class="mt-5 grid gap-5 sm:grid-cols-3">
            <div>
                <label for="theme_schedule_theme" class="label">Theme to activate</label>
                <select id="theme_schedule_theme" name="theme_schedule_theme" class="input">
                    <option value="">— Select —</option>
                    @foreach ($themes as $key => $theme)
                        <option value="{{ $key }}" {{ $schedule['theme'] === $key ? 'selected' : '' }}>{{ $theme['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="theme_schedule_start" class="label">Start</label>
                <input id="theme_schedule_start" name="theme_schedule_start" type="datetime-local" value="{{ $schedule['start'] ? \Illuminate\Support\Carbon::parse($schedule['start'])->format('Y-m-d\TH:i') : '' }}" class="input">
            </div>
            <div>
                <label for="theme_schedule_end" class="label">End</label>
                <input id="theme_schedule_end" name="theme_schedule_end" type="datetime-local" value="{{ $schedule['end'] ? \Illuminate\Support\Carbon::parse($schedule['end'])->format('Y-m-d\TH:i') : '' }}" class="input">
            </div>
        </div>
        <div class="mt-5 flex justify-end">
            <button type="submit" class="btn-primary btn-md">Save schedule</button>
        </div>
    </form>
</div>
@endsection
