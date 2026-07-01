@extends('layouts.admin')

@section('page-title', 'Advertisement')

@section('admin')
<div class="mx-auto max-w-5xl space-y-8">

    {{-- Intro --}}
    <div>
        <h2 class="font-display text-xl font-extrabold text-ink-900">Advertisement banners</h2>
        <p class="mt-1 text-sm text-slate-500">
            Show banner ads at the bottom of key pages. Use Google AdSense / Meta Audience Network code,
            or upload your own manual banners. Ads render in-flow at the page bottom so they never block the UI.
        </p>
    </div>

    {{-- Settings --}}
    <form method="POST" action="{{ route('admin.ads.settings') }}" class="card space-y-6 p-6" x-data="{ enabled: {{ $enabled ? 'true' : 'false' }} }">
        @csrf
        @method('PUT')

        {{-- Master toggle --}}
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <p class="font-display text-base font-bold text-ink-900">Enable advertisement banners</p>
                <p class="mt-1 text-sm text-slate-500">Master switch. When off, no ads are shown anywhere on the site.</p>
            </div>
            <label class="relative inline-flex cursor-pointer items-center">
                <input type="checkbox" name="ads_enabled" value="1" class="peer sr-only" x-model="enabled">
                <div class="peer h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand-500 peer-checked:after:translate-x-full"></div>
            </label>
        </div>

        <div :class="enabled ? '' : 'pointer-events-none opacity-50'" class="space-y-6 transition">
            {{-- Network codes --}}
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="ads_adsense_code" class="label">Google AdSense code</label>
                    <textarea id="ads_adsense_code" name="ads_adsense_code" rows="5" class="input font-mono text-xs"
                              placeholder="&lt;script async src=...&gt;&lt;/script&gt;&lt;ins class=&quot;adsbygoogle&quot; ...&gt;&lt;/ins&gt;">{{ old('ads_adsense_code', $adsenseCode) }}</textarea>
                    <p class="mt-1 text-xs text-slate-400">Paste your AdSense ad unit snippet.</p>
                </div>
                <div>
                    <label for="ads_meta_code" class="label">Meta / other ad network code</label>
                    <textarea id="ads_meta_code" name="ads_meta_code" rows="5" class="input font-mono text-xs"
                              placeholder="&lt;script&gt;...&lt;/script&gt; or &lt;div&gt;...&lt;/div&gt; embed code">{{ old('ads_meta_code', $metaCode) }}</textarea>
                    <p class="mt-1 text-xs text-slate-400">Meta Audience Network or any other embed code.</p>
                </div>
            </div>

            <div class="rounded-xl bg-amber-50 px-4 py-3 text-xs text-amber-700">
                <strong>Priority:</strong> If AdSense or Meta code is set, it takes priority over your manual ads below.
                Manual banners are only shown when both code fields are empty.
            </div>

            {{-- Layout --}}
            <div class="max-w-xs">
                <label for="ads_layout" class="label">Ads per row (manual banners)</label>
                <select id="ads_layout" name="ads_layout" class="input">
                    @foreach ([1 => '1 — Full-width banner', 2 => '2 per row', 3 => '3 per row', 4 => '4 per row', 6 => '6 per row', 8 => '8 per row'] as $opt => $optLabel)
                        <option value="{{ $opt }}" {{ (int) old('ads_layout', $layout) === $opt ? 'selected' : '' }}>{{ $optLabel }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-400">Choose a grid style — from a single full-width banner up to 8 compact tiles per row.</p>
            </div>

            {{-- Per-page toggles --}}
            <div>
                <p class="label">Show banner on these pages</p>
                <div class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                    @php
                        $pageLabels = [
                            'marketplace' => 'Marketplace',
                            'cart' => 'Cart',
                            'checkout' => 'Checkout',
                            'dashboard' => 'User Dashboard',
                            'purchases' => 'My Purchases',
                            'wishlist' => 'Wishlist',
                            'support' => 'Support',
                            'home_free' => 'Home — Free section',
                            'home_reviews' => 'Home — Below reviews',
                            'pages' => 'Custom pages',
                            'contact' => 'Contact us',
                        ];
                    @endphp
                    @foreach ($pageLabels as $key => $label)
                        <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-ink-900">
                            <input type="checkbox" name="ads_page_{{ $key }}" value="1" {{ ($pages[$key] ?? true) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex justify-end border-t border-slate-100 pt-5">
            <button type="submit" class="btn-primary btn-md">Save settings</button>
        </div>
    </form>

    {{-- Manual ads --}}
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <h3 class="font-display text-lg font-bold text-ink-900">Manual banner ads</h3>
            <span class="chip bg-slate-100 text-slate-600">{{ $ads->count() }} total</span>
        </div>
        <p class="mt-1 text-sm text-slate-500">Recommended up to ~10 banners for a clean layout.</p>

        {{-- Existing ads list --}}
        <div class="mt-5 space-y-3">
            @forelse ($ads as $ad)
                <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 p-4 sm:flex-row sm:items-center">
                    <img src="{{ $ad->display_image }}" alt="{{ $ad->title }}" class="h-20 w-32 flex-shrink-0 rounded-lg object-cover">
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-bold text-ink-900">{{ $ad->title ?: 'Untitled ad' }}</p>
                        @if ($ad->link_url)
                            <p class="mt-0.5 truncate text-xs text-slate-400">{{ $ad->link_url }}</p>
                        @endif
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            @if ($ad->is_active)
                                <span class="chip bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">Active</span>
                            @else
                                <span class="chip bg-slate-100 text-slate-500">Hidden</span>
                            @endif
                            <span class="chip bg-slate-100 text-slate-500">Order {{ $ad->sort_order }}</span>
                        </div>
                    </div>

                    {{-- Inline edit --}}
                    <div x-data="{ edit: false }" class="flex-shrink-0">
                        <div class="flex items-center gap-2">
                            <button type="button" @click="edit = !edit" class="btn-ghost btn-sm">Edit</button>
                            <form method="POST" action="{{ route('admin.ads.destroy', $ad) }}" onsubmit="return confirm('Delete this ad?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-ghost btn-sm text-rose-600 hover:bg-rose-50">Delete</button>
                            </form>
                        </div>

                        <form x-show="edit" x-cloak method="POST" action="{{ route('admin.ads.update', $ad) }}" enctype="multipart/form-data"
                              class="mt-3 w-full space-y-3 rounded-xl bg-slate-50 p-4 sm:w-80">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="label">Title</label>
                                <input type="text" name="title" value="{{ $ad->title }}" class="input" maxlength="120">
                            </div>
                            <div>
                                <label class="label">Replace image</label>
                                <input type="file" name="image" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-600">
                            </div>
                            <div>
                                <label class="label">Image URL</label>
                                <input type="url" name="image_url" value="{{ $ad->image_url }}" class="input" placeholder="https://...">
                            </div>
                            <div>
                                <label class="label">Link URL</label>
                                <input type="url" name="link_url" value="{{ $ad->link_url }}" class="input" placeholder="https://...">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="label">Sort order</label>
                                    <input type="number" name="sort_order" value="{{ $ad->sort_order }}" min="0" class="input">
                                </div>
                                <label class="mt-6 flex items-center gap-2 text-sm font-semibold text-ink-900">
                                    <input type="checkbox" name="is_active" value="1" {{ $ad->is_active ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                                    Active
                                </label>
                            </div>
                            <button type="submit" class="btn-primary btn-sm w-full">Save changes</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="rounded-xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-400">No manual ads yet. Add your first banner below.</p>
            @endforelse
        </div>

        {{-- Add ad form --}}
        <form method="POST" action="{{ route('admin.ads.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4 border-t border-slate-100 pt-6">
            @csrf
            <h4 class="font-display text-sm font-bold text-ink-900">Add a banner</h4>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="title" class="label">Title (optional)</label>
                    <input id="title" type="text" name="title" value="{{ old('title') }}" class="input" maxlength="120" placeholder="e.g. Summer Sale">
                </div>
                <div>
                    <label for="link_url" class="label">Link URL (optional)</label>
                    <input id="link_url" type="url" name="link_url" value="{{ old('link_url') }}" class="input" placeholder="https://example.com">
                </div>
                <div>
                    <label for="image" class="label">Upload image</label>
                    <input id="image" type="file" name="image" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-600">
                    <p class="mt-1 text-xs text-slate-400">Max 2MB. JPG / PNG / WEBP.</p>
                </div>
                <div>
                    <label for="image_url" class="label">…or image URL</label>
                    <input id="image_url" type="url" name="image_url" value="{{ old('image_url') }}" class="input" placeholder="https://.../banner.jpg">
                    <p class="mt-1 text-xs text-slate-400">Provide an upload or a URL.</p>
                </div>
                <div>
                    <label for="sort_order" class="label">Sort order</label>
                    <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="input">
                </div>
                <label class="mt-7 flex items-center gap-2 text-sm font-semibold text-ink-900">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                    Active
                </label>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="btn-primary btn-md">Add ad</button>
            </div>
        </form>
    </div>
</div>
@endsection
