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
                <div class="animate-fade-up">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3.5 py-1.5 text-xs font-semibold text-brand-200">
                        <span class="flex h-1.5 w-1.5 rounded-full bg-brand-400"></span>
                        Trusted by thousands of builders
                    </span>
                    <h1 class="mt-5 font-display text-4xl font-extrabold leading-[1.1] tracking-tight text-white sm:text-5xl lg:text-6xl">
                        {{ number_format($stats['products']) }}+ curated digital products<br class="hidden sm:block"> for your <span class="gradient-text">next project</span>
                    </h1>
                    <p class="mt-5 max-w-lg text-base text-slate-300">
                        Buy and download premium scripts, source code, UI kits and design assets. Instant delivery, lifetime access and updates included.
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
                <div class="relative hidden lg:block">
                    <div class="animate-floaty overflow-hidden rounded-2xl border border-white/10 bg-white shadow-2xl">
                        {{-- Browser bar --}}
                        <div class="flex items-center gap-2 border-b border-slate-100 px-4 py-3">
                            <span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                            <span class="ml-3 flex-1 truncate rounded-md bg-slate-100 px-3 py-1 text-[10px] text-slate-400">akzonescripts.com/marketplace</span>
                        </div>
                        {{-- Tabs --}}
                        <div class="flex items-center gap-2 px-4 pt-4">
                            <span class="rounded-full bg-brand-600 px-3 py-1 text-[11px] font-semibold text-white">All</span>
                            @foreach (['Scripts', 'UI Kits', 'Mobile'] as $tab)
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-500">{{ $tab }}</span>
                            @endforeach
                        </div>
                        {{-- Product tiles --}}
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
                    </div>
                    {{-- Floating stat cards --}}
                    <div class="absolute -left-6 top-10 rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-lift">
                        <p class="text-[10px] text-slate-400">Total downloads</p>
                        <p class="font-display text-lg font-extrabold text-ink-900">{{ number_format($stats['downloads']) }}</p>
                    </div>
                    <div class="absolute -bottom-5 left-8 flex items-center gap-2 rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-lift">
                        <x-star-rating :rating="5" size="h-4 w-4" />
                        <span class="text-sm font-bold text-ink-900">5.0</span>
                    </div>
                    <div class="absolute -right-4 bottom-16 flex items-center gap-2 rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-lift">
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
            <div class="relative mt-12 grid grid-cols-3 gap-4 border-t border-white/10 pt-8 text-center sm:max-w-lg">
                <div><p class="font-display text-2xl font-extrabold text-white sm:text-3xl">{{ number_format($stats['products']) }}+</p><p class="text-xs text-slate-400">Products</p></div>
                <div><p class="font-display text-2xl font-extrabold text-white sm:text-3xl">{{ number_format($stats['downloads']) }}+</p><p class="text-xs text-slate-400">Downloads</p></div>
                <div><p class="font-display text-2xl font-extrabold text-white sm:text-3xl">{{ number_format($stats['categories']) }}</p><p class="text-xs text-slate-400">Categories</p></div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="pt-6">@include('partials.flash')</div>

        {{-- Categories --}}
        @if ($categories->isNotEmpty())
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
        @if ($latest->isNotEmpty())
            <div class="py-8">
                <x-product-carousel :products="$latest" eyebrow="Latest" title="Check out latest items"
                    subtitle="Fresh uploads added to the marketplace." :view-all="route('products.index')" />
            </div>
        @endif
    </div>

    {{-- Featured (slate band) --}}
    @if ($featured->isNotEmpty())
        <section class="reveal bg-slate-100/70 py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-product-carousel :products="$featured" eyebrow="Featured" title="The world-leading marketplace"
                    subtitle="Hand-picked, top-quality digital products." :view-all="route('products.index')" />
            </div>
        </section>
    @endif

    {{-- Best selling (green band) --}}
    @if ($bestSelling->isNotEmpty())
        <section class="reveal bg-emerald-50/60 py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-product-carousel :products="$bestSelling" eyebrow="Popular" title="Weekly best selling items"
                    subtitle="What other builders are buying right now." :view-all="route('products.index', ['sort' => 'popular'])" />
            </div>
        </section>
    @endif

    {{-- Free items band (pastel gradient) --}}
    @if ($freeItems->isNotEmpty())
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
                <div x-ref="t" class="no-scrollbar -mx-1 flex snap-x gap-5 overflow-x-auto scroll-smooth px-1 pb-2">
                    @foreach ($freeItems as $product)
                        <x-product-card :product="$product" class="w-[270px] flex-none snap-start sm:w-[290px]" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- Testimonials --}}
        <section class="reveal py-16">
            <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-600"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>Testimonials</span>
                    <h2 class="mt-2 section-title">What customers are saying</h2>
                    <p class="mt-1.5 text-sm text-slate-500">Trusted by builders across the marketplace.</p>
                </div>
                {{-- Big 5.0 rating box --}}
                <div class="flex items-center gap-4 rounded-2xl bg-ink-900 px-6 py-4 text-white shadow-lift">
                    <div>
                        <p class="font-display text-4xl font-extrabold leading-none">5.0</p>
                        <div class="mt-1.5 flex">
                            @for ($i = 0; $i < 5; $i++)
                                <svg class="h-4 w-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z" /></svg>
                            @endfor
                        </div>
                    </div>
                    <div class="h-12 w-px bg-white/15"></div>
                    <p class="max-w-[8rem] text-xs text-slate-300">Average rating from happy buyers</p>
                </div>
            </div>

            @php
                $testimonials = [
                    ['name' => 'Daniel R.', 'role' => 'Full-stack Developer', 'text' => 'The code quality is outstanding and saved me weeks of work. Documentation made setup a breeze.'],
                    ['name' => 'Aisha K.', 'role' => 'Startup Founder', 'text' => 'Found exactly what we needed to launch our MVP. Instant download and great support.'],
                    ['name' => 'Marco P.', 'role' => 'UI/UX Designer', 'text' => 'Beautiful UI kits and assets. Everything is clean, modern and easy to customise.'],
                ];
            @endphp
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                @foreach ($testimonials as $i => $t)
                    <figure class="reveal reveal-delay-{{ $i + 1 }} card p-6">
                        <x-star-rating :rating="5" size="h-4 w-4" />
                        <blockquote class="mt-4 text-sm leading-relaxed text-slate-600">"{{ $t['text'] }}"</blockquote>
                        <figcaption class="mt-5 flex items-center gap-3 border-t border-slate-100 pt-4">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-indigo-500 text-sm font-bold text-white">{{ substr($t['name'], 0, 1) }}</span>
                            <div>
                                <p class="text-sm font-bold text-ink-900">{{ $t['name'] }}</p>
                                <p class="text-xs text-slate-400">{{ $t['role'] }}</p>
                            </div>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </section>

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
