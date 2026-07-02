@extends('layouts.app')

@section('flash-wrap', true)

@section('hero')
    <section class="px-4 pt-4 sm:px-6 lg:px-8">
        <div class="relative mx-auto max-w-7xl overflow-hidden rounded-[2rem] bg-ink-900 px-6 py-12 sm:px-10 lg:py-16">
            <div class="dot-grid absolute inset-0 opacity-60"></div>
            <div class="absolute -right-24 -top-24 h-80 w-80 rounded-full bg-brand-600/30 blur-3xl"></div>
            <div class="absolute -bottom-24 left-1/3 h-72 w-72 rounded-full bg-indigo-500/20 blur-3xl"></div>

            <div class="relative grid items-center gap-10 lg:grid-cols-2">
                {{-- Left --}}
                <div class="order-2 animate-fade-up lg:order-1">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3.5 py-1.5 text-xs font-semibold text-brand-200">
                        <span class="flex h-1.5 w-1.5 rounded-full bg-brand-400"></span>
                        {{ setting('hero_badge', 'Trusted by thousands of builders') }}
                    </span>
                    @php
                        $heroTitle = e(setting('hero_title', 'curated digital products for your next project'));
                        $heroHighlight = setting('hero_highlight', 'next project');
                        $heroTitleHtml = $heroHighlight
                            ? str_replace(e($heroHighlight), '<span class="gradient-text">'.e($heroHighlight).'</span>', $heroTitle)
                            : $heroTitle;
                    @endphp
                    <h1 class="mt-5 font-display text-4xl font-extrabold leading-[1.1] tracking-tight text-white sm:text-5xl lg:text-6xl">
                        {{ number_format($stats['products']) }}+ {!! $heroTitleHtml !!}
                    </h1>
                    <p class="mt-5 max-w-lg text-base text-slate-300">
                        {{ setting('hero_subtitle', 'Buy and download premium scripts, source code, UI kits and design assets. Instant delivery, lifetime access and updates included.') }}
                    </p>

                    <form action="{{ route('products.index') }}" method="GET" class="mt-7 flex max-w-md items-center gap-2 rounded-2xl bg-white p-1.5 shadow-lift">
                        <svg class="ml-2.5 h-5 w-5 flex-shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                        <input type="text" name="q" placeholder="Search scripts, templates, UI kits..." class="w-full border-0 bg-transparent py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-0">
                        <button type="submit" class="btn-primary btn-md flex-shrink-0">Browse</button>
                    </form>

                    {{-- Category quick tiles (colorful) --}}
                    <div class="mt-7 flex flex-wrap gap-2.5">
                        @php $tileTints = ['from-sky-400 to-blue-500','from-violet-400 to-purple-500','from-rose-400 to-pink-500','from-amber-400 to-orange-500','from-emerald-400 to-teal-500','from-indigo-400 to-blue-500','from-fuchsia-400 to-pink-500','from-cyan-400 to-sky-500']; @endphp
                        @foreach ($categories->take(8) as $i => $category)
                            <a href="{{ route('categories.show', $category) }}" title="{{ $category->name }}"
                               class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br {{ $tileTints[$i % count($tileTints)] }} text-xl shadow-lg transition hover:-translate-y-1">
                                {{ $category->icon ?: '📦' }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Right: browser-style preview --}}
                <div class="relative order-1 mb-4 lg:order-2 lg:mb-0">
                    <div class="animate-floaty overflow-hidden rounded-2xl border border-white/10 bg-white shadow-2xl">
                        {{-- Browser bar --}}
                        <div class="flex items-center gap-2 border-b border-slate-100 px-4 py-3">
                            <span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                            <span class="ml-3 flex-1 truncate rounded-md bg-slate-100 px-3 py-1 text-[10px] text-slate-400">{{ \Illuminate\Support\Str::of(route('products.index'))->after('://')->rtrim('/') }}</span>
                        </div>
                        {{-- Tabs --}}
                        <div class="flex items-center gap-2 px-4 pt-4">
                            <span class="rounded-full bg-brand-600 px-3 py-1 text-[11px] font-semibold text-white">All</span>
                            @foreach (['Scripts', 'UI Kits', 'Mobile'] as $tab)
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-500">{{ $tab }}</span>
                            @endforeach
                        </div>
                        {{-- Box content — promotion-aware (managed from Admin → Promotions) --}}
                        @php $promoMode = $promotion['mode'] ?? 'off'; @endphp
                        @if ($promoMode === 'products')
                            <div class="grid grid-cols-2 gap-3 p-4">
                                @foreach ($promotion['products'] as $promoProduct)
                                    <a href="{{ route('products.show', $promoProduct) }}" class="group rounded-2xl border-2 border-slate-200 bg-white p-2.5 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-400 hover:shadow-md">
                                        <img src="{{ $promoProduct->thumbnail_url }}" alt="{{ $promoProduct->title }}" class="h-20 w-full rounded-xl border border-slate-100 object-cover">
                                        <p class="mt-2 truncate text-xs font-semibold text-ink-900 group-hover:text-brand-600">{{ $promoProduct->title }}</p>
                                        <div class="mt-1 flex items-center justify-between">
                                            <span class="text-xs font-bold text-brand-600">{{ money($promoProduct->current_price) }}</span>
                                            <span class="rounded-md bg-brand-50 px-1.5 py-0.5 text-[9px] font-semibold text-brand-600">View</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @elseif ($promoMode === 'message')
                            <div class="relative flex min-h-[240px] flex-col items-center justify-center gap-4 overflow-hidden bg-gradient-to-br from-brand-50 via-indigo-50 to-rose-50 p-7 text-center">
                                <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-brand-300/30 blur-2xl"></div>
                                <div class="absolute -bottom-10 -left-8 h-32 w-32 rounded-full bg-indigo-300/30 blur-2xl"></div>
                                <span class="relative flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-indigo-500 text-white shadow-lift">
                                    <span class="absolute inset-0 animate-ping rounded-2xl bg-brand-400/40"></span>
                                    <svg class="relative h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>
                                </span>
                                <p class="relative max-w-sm font-display text-lg font-bold leading-snug text-ink-900">{{ $promotion['message'] }}</p>
                                @if (! empty($promotion['url']))
                                    <a href="{{ $promotion['url'] }}" class="relative btn-primary btn-md">
                                        Learn more
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                                    </a>
                                @endif
                            </div>
                        @elseif ($promoMode === 'countdown')
                            <div class="space-y-3 p-4">
                                @foreach ($promotion['offers'] as $offer)
                                    @php $cp = $offer['product']; @endphp
                                    <a href="{{ route('products.show', $cp) }}"
                                       x-data="{ end: new Date('{{ $offer['until'] }}').getTime(), now: Date.now(),
                                                 get diff(){ return Math.max(0, this.end - this.now) },
                                                 get d(){ return Math.floor(this.diff/864e5) },
                                                 get h(){ return Math.floor(this.diff%864e5/36e5) },
                                                 get m(){ return Math.floor(this.diff%36e5/6e4) },
                                                 get s(){ return Math.floor(this.diff%6e4/1e3) },
                                                 pad(n){ return String(n).padStart(2,'0') },
                                                 init(){ setInterval(() => this.now = Date.now(), 1000) } }"
                                       class="block rounded-2xl border-2 border-amber-200 bg-gradient-to-br from-amber-50 to-rose-50 p-3 transition hover:-translate-y-0.5 hover:border-amber-300 hover:shadow-md">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-700">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                            {{ $offer['label'] }}
                                        </span>
                                        <div class="mt-2.5 flex items-center gap-3">
                                            <img src="{{ $cp->thumbnail_url }}" alt="{{ $cp->title }}" class="h-14 w-14 flex-shrink-0 rounded-xl border border-amber-200 object-cover">
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-bold text-ink-900">{{ $cp->title }}</p>
                                                <p class="mt-0.5 text-sm font-bold text-brand-600">
                                                    {{ money($cp->current_price) }}
                                                    @if ($cp->is_on_sale)<span class="ml-1 text-xs text-slate-400 line-through">{{ money($cp->price) }}</span>@endif
                                                </p>
                                            </div>
                                        </div>
                                        <template x-if="diff > 0">
                                            <div class="mt-2.5 grid grid-cols-4 gap-1.5">
                                                @foreach (['d' => 'Days', 'h' => 'Hrs', 'm' => 'Min', 's' => 'Sec'] as $unit => $unitLabel)
                                                    <div class="flex flex-col items-center justify-center rounded-lg bg-slate-900 py-1.5 text-white">
                                                        <span class="font-display text-base font-extrabold" x-text="pad({{ $unit }})">00</span>
                                                        <span class="text-[8px] uppercase text-white/50">{{ $unitLabel }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </template>
                                        <template x-if="diff <= 0">
                                            <div class="mt-2.5 rounded-lg bg-rose-50 py-2 text-center text-xs font-bold text-rose-500">Offer ended</div>
                                        </template>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            {{-- Default placeholder tiles --}}
                            <div class="grid grid-cols-3 gap-3 p-4">
                                @php $previewColors = ['from-brand-100 to-brand-50','from-indigo-100 to-indigo-50','from-rose-100 to-rose-50','from-emerald-100 to-emerald-50','from-amber-100 to-amber-50','from-violet-100 to-violet-50']; @endphp
                                @foreach ($previewColors as $c)
                                    <div class="rounded-xl bg-gradient-to-br {{ $c }} p-2.5">
                                        <div class="h-10 rounded-lg bg-white/70"></div>
                                        <div class="mt-2 h-1.5 w-3/4 rounded bg-slate-300/70"></div>
                                        <div class="mt-1.5 flex items-center justify-between">
                                            <div class="h-1.5 w-1/3 rounded bg-slate-300/50"></div>
                                            <div class="h-3 w-6 rounded bg-brand-500/40"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    {{-- Floating stat cards --}}
                    <div class="absolute -left-6 top-10 hidden rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-lift sm:block">
                        <p class="text-[10px] text-slate-400">Total sold</p>
                        <p class="font-display text-lg font-extrabold text-ink-900">{{ number_format($stats['sold']) }}</p>
                    </div>
                    <div class="absolute -bottom-5 left-8 hidden items-center gap-2 rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-lift sm:flex">
                        <x-star-rating :rating="5" size="h-4 w-4" />
                        <span class="text-sm font-bold text-ink-900">5.0</span>
                    </div>
                    <div class="absolute -right-4 bottom-16 hidden items-center gap-2 rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-lift sm:flex">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                        <div>
                            <p class="text-[10px] text-slate-400">Instant</p>
                            <p class="text-xs font-bold text-ink-900">Delivery</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stat strip --}}
            <div class="relative mt-12 grid grid-cols-2 gap-4 border-t border-white/10 pt-8 text-center sm:max-w-2xl sm:grid-cols-4">
                <div><p class="font-display text-2xl font-extrabold text-white sm:text-3xl">{{ number_format($stats['products']) }}+</p><p class="text-xs text-slate-400">Products</p></div>
                <div><p class="font-display text-2xl font-extrabold text-white sm:text-3xl">{{ number_format($stats['sold']) }}+</p><p class="text-xs text-slate-400">Sold</p></div>
                <div><p class="font-display text-2xl font-extrabold text-white sm:text-3xl">{{ number_format($stats['free']) }}</p><p class="text-xs text-slate-400">Free items</p></div>
                <div><p class="font-display text-2xl font-extrabold text-white sm:text-3xl">{{ number_format($stats['downloads']) }}+</p><p class="text-xs text-slate-400">Downloads</p></div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="pt-6">@include('partials.flash')</div>

        {{-- Categories --}}
        @if ($categories->isNotEmpty() && setting('home_show_categories', '1') !== '0')
            <section id="categories" class="reveal py-12">
                <div class="mb-6 flex items-end justify-between">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-600"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>Categories</span>
                        <h2 class="mt-2 section-title">Browse by category</h2>
                    </div>
                    <a href="{{ route('products.index') }}" class="hidden rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-ink-900 transition hover:border-brand-300 hover:text-brand-600 sm:block">View All</a>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    @php $catTints = ['bg-sky-50 text-sky-600','bg-violet-50 text-violet-600','bg-rose-50 text-rose-600','bg-amber-50 text-amber-600','bg-emerald-50 text-emerald-600','bg-indigo-50 text-indigo-600']; @endphp
                    @foreach ($categories as $i => $category)
                        <a href="{{ route('categories.show', $category) }}"
                           class="group flex flex-col items-center gap-3 rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-soft transition hover:-translate-y-1 hover:border-brand-200 hover:shadow-lift">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl {{ $catTints[$i % count($catTints)] }} text-2xl transition group-hover:scale-110">{{ $category->icon ?: '📦' }}</span>
                            <div>
                                <p class="text-sm font-bold text-ink-900 group-hover:text-brand-600">{{ $category->name }}</p>
                                <p class="text-xs text-slate-400">{{ $category->published_products_count }} items</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Latest (white) --}}
        @if ($latest->isNotEmpty() && setting('home_show_latest', '1') !== '0')
            <div class="py-8">
                <x-product-carousel :products="$latest" eyebrow="Latest" title="Check out latest items"
                    subtitle="Fresh uploads added to the marketplace." :view-all="route('products.index')" />
            </div>
        @endif
    </div>

    {{-- Featured (slate band) --}}
    @if ($featured->isNotEmpty() && setting('home_show_featured', '1') !== '0')
        <section class="reveal bg-slate-100/70 py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-product-carousel :products="$featured" eyebrow="Featured" title="The world-leading marketplace"
                    subtitle="Hand-picked, top-quality digital products." :view-all="route('products.index')" />
            </div>
        </section>
    @endif

    {{-- Best selling (green band) --}}
    @if ($bestSelling->isNotEmpty() && setting('home_show_bestselling', '1') !== '0')
        <section class="reveal bg-emerald-50/60 py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-product-carousel :products="$bestSelling" eyebrow="Popular" title="Weekly best selling items"
                    subtitle="What other builders are buying right now." :view-all="route('products.index', ['sort' => 'popular'])" />
            </div>
        </section>
    @endif

    {{-- Limited Deal band (limited-time offers + soon-out-of-stock) --}}
    @if ($limitedDeals->isNotEmpty())
        <section class="reveal bg-gradient-to-br from-ink-900 via-brand-700 to-brand-500 py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" x-data="{}" x-ref="ld">
                <div class="mb-6 flex items-end justify-between">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            Limited Deal
                        </span>
                        <h2 class="mt-2 font-display text-2xl font-extrabold tracking-tight text-white sm:text-3xl">Limited-time offers &amp; low stock</h2>
                        <p class="mt-1.5 text-sm text-white/80">Grab these before the timer runs out or they sell out.</p>
                    </div>
                    <a href="{{ route('products.index') }}" class="hidden text-sm font-semibold text-white hover:underline sm:inline">View all &rarr;</a>
                </div>
                <div class="no-scrollbar -mx-1 flex snap-x gap-4 overflow-x-auto scroll-smooth px-1 pb-2 sm:gap-5">
                    @foreach ($limitedDeals as $product)
                        <x-product-card :product="$product" class="w-[46%] flex-none snap-start sm:w-[290px]" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('partials.ads', ['page' => 'home_free'])

    {{-- Free items band (pastel gradient) --}}
    @if ($freeItems->isNotEmpty() && setting('home_show_free', '1') !== '0')
        <section class="reveal bg-gradient-to-br from-brand-600 to-indigo-600 py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8"
                 x-data="{ scroll(dir){ $refs.t.scrollBy({left: dir*$refs.t.clientWidth*0.85, behavior:'smooth'}) } }">
                <div class="mb-6 flex items-end justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wide text-white"><span class="h-1.5 w-1.5 rounded-full bg-white"></span>Free</span>
                        <h2 class="mt-2 font-display text-2xl font-extrabold tracking-tight text-white sm:text-3xl">Download free items</h2>
                        <p class="mt-1.5 text-sm text-brand-100">Premium quality, zero cost. Grab them while they're free.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('products.index') }}" class="hidden rounded-full bg-white/15 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/25 sm:block">View all items</a>
                        <button type="button" @click="scroll(-1)" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/25"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg></button>
                        <button type="button" @click="scroll(1)" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white/25"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg></button>
                    </div>
                </div>
                <div x-ref="t" class="no-scrollbar -mx-1 flex snap-x gap-4 overflow-x-auto scroll-smooth px-1 pb-2 sm:gap-5">
                    @foreach ($freeItems as $product)
                        <x-product-card :product="$product" class="w-[46%] flex-none snap-start sm:w-[290px]" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @if (setting('home_show_testimonials', '1') !== '0')
        {{-- Testimonials --}}
        <section class="reveal py-16"
                 x-data="{ scroll(dir){ $refs.tt.scrollBy({left: dir*$refs.tt.clientWidth*0.9, behavior:'smooth'}) } }">
            <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-600">
                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z" /></svg>
                        Verified Reviews
                    </span>
                    <h2 class="mt-2 section-title">What customers are saying</h2>
                    <p class="mt-1.5 text-sm text-slate-500">Real feedback from real buyers across our marketplace.</p>
                </div>
                <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white px-6 py-4 shadow-soft">
                    <div>
                        <p class="font-display text-4xl font-extrabold leading-none text-ink-900">5.0</p>
                        <div class="mt-1.5 flex">
                            @for ($i = 0; $i < 5; $i++)
                                <svg class="h-4 w-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z" /></svg>
                            @endfor
                        </div>
                    </div>
                    @if ($testimonials->isNotEmpty())
                        <div class="h-12 w-px bg-slate-200"></div>
                        <p class="text-xs text-slate-400">{{ $testimonials->count() }} {{ Str::plural('review', $testimonials->count()) }}</p>
                    @endif
                </div>
            </div>

            @php
                $fallback = collect([
                    (object) ['name' => 'Daniel R.', 'comment' => 'The code quality is outstanding and saved me weeks of work. Documentation made setup a breeze.', 'product' => 'InvoiceFlow'],
                    (object) ['name' => 'Aisha K.', 'comment' => 'Found exactly what we needed to launch our MVP. Instant download and great support.', 'product' => 'LaraCommerce'],
                    (object) ['name' => 'Marco P.', 'comment' => 'Beautiful UI kits and assets. Everything is clean, modern and easy to customise.', 'product' => 'Nebula'],
                ]);
            @endphp

            <div x-ref="tt" class="no-scrollbar mt-8 flex snap-x gap-5 overflow-x-auto scroll-smooth pb-2">
                @forelse ($testimonials as $t)
                    <figure class="w-[85%] flex-none snap-start rounded-2xl border border-slate-200 bg-white p-6 shadow-soft sm:w-[46%] lg:w-[31%]">
                        <div class="flex items-center justify-between">
                            <x-star-rating :rating="$t->rating" size="h-4 w-4" />
                            <span class="text-xs text-slate-400">{{ $t->created_at->diffForHumans() }}</span>
                        </div>
                        <blockquote class="mt-4 text-sm leading-relaxed text-slate-600">"{{ $t->comment ?: 'Great product!' }}"</blockquote>
                        <figcaption class="mt-5 flex items-center gap-3 border-t border-slate-100 pt-4">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-indigo-500 text-sm font-bold text-white">{{ strtoupper(substr($t->user->name ?? 'A', 0, 1)) }}</span>
                            <div>
                                <p class="text-sm font-bold text-ink-900">{{ $t->user->name ?? 'Verified user' }}</p>
                                <p class="flex items-center gap-1 text-xs text-emerald-600">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                                    Verified Buyer
                                </p>
                            </div>
                        </figcaption>
                    </figure>
                @empty
                    @foreach ($fallback as $t)
                        <figure class="w-[85%] flex-none snap-start rounded-2xl border border-slate-200 bg-white p-6 shadow-soft sm:w-[46%] lg:w-[31%]">
                            <div class="flex items-center justify-between">
                                <x-star-rating :rating="5" size="h-4 w-4" />
                                <span class="text-xs text-slate-400">recently</span>
                            </div>
                            <blockquote class="mt-4 text-sm leading-relaxed text-slate-600">"{{ $t->comment }}"</blockquote>
                            <figcaption class="mt-5 flex items-center gap-3 border-t border-slate-100 pt-4">
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-indigo-500 text-sm font-bold text-white">{{ substr($t->name, 0, 1) }}</span>
                                <div>
                                    <p class="text-sm font-bold text-ink-900">{{ $t->name }}</p>
                                    <p class="flex items-center gap-1 text-xs text-emerald-600">
                                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                                        Verified Buyer
                                    </p>
                                </div>
                            </figcaption>
                        </figure>
                    @endforeach
                @endforelse
            </div>
        </section>
        @endif

        @include('partials.ads', ['page' => 'home_reviews'])

        {{-- CTA --}}
        <section class="reveal pb-16">
            <div class="relative overflow-hidden rounded-[2rem] bg-ink-900 px-8 py-14 text-center sm:px-14">
                <div class="dot-grid absolute inset-0 opacity-50"></div>
                <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-brand-600/30 blur-3xl"></div>
                <div class="relative mx-auto max-w-2xl">
                    <h2 class="font-display text-3xl font-extrabold text-white sm:text-4xl">Ready to ship your next big idea?</h2>
                    <p class="mt-4 text-lg text-slate-300">Join thousands of developers and designers saving weeks of work with battle-tested code and assets.</p>
                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        <a href="{{ route('products.index') }}" class="btn-primary btn-lg">Browse marketplace</a>
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-lg border border-white/20 text-white hover:bg-white/10">Create free account</a>
                        @endguest
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
