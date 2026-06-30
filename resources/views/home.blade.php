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

                    {{-- Category quick tiles --}}
                    <div class="mt-7 flex flex-wrap gap-2.5">
                        @foreach ($categories->take(6) as $category)
                            <a href="{{ route('categories.show', $category) }}" title="{{ $category->name }}"
                               class="flex h-11 w-11 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-xl transition hover:-translate-y-1 hover:bg-white/10">
                                {{ $category->icon ?: '📦' }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Right: preview mock --}}
                <div class="relative hidden lg:block">
                    <div class="animate-floaty rounded-2xl border border-white/10 bg-white p-4 shadow-2xl">
                        <div class="flex items-center gap-1.5 pb-3">
                            <span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            @php $previewColors = ['from-brand-100 to-brand-50','from-indigo-100 to-indigo-50','from-rose-100 to-rose-50','from-emerald-100 to-emerald-50','from-amber-100 to-amber-50','from-violet-100 to-violet-50']; @endphp
                            @foreach ($previewColors as $i => $c)
                                <div class="rounded-xl bg-gradient-to-br {{ $c }} p-3">
                                    <div class="h-12 rounded-lg bg-white/70"></div>
                                    <div class="mt-2 h-2 w-3/4 rounded bg-slate-300/70"></div>
                                    <div class="mt-1.5 h-2 w-1/2 rounded bg-slate-300/50"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- Floating stat chips --}}
                    <div class="absolute -left-6 top-6 rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-lift">
                        <p class="text-xs text-slate-400">Total downloads</p>
                        <p class="font-display text-xl font-extrabold text-ink-900">{{ number_format($stats['downloads']) }}</p>
                    </div>
                    <div class="absolute -bottom-5 right-4 flex items-center gap-2 rounded-2xl border border-slate-100 bg-white px-4 py-3 shadow-lift">
                        <x-star-rating :rating="5" size="h-4 w-4" />
                        <span class="text-sm font-bold text-ink-900">5.0</span>
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
                        <span class="section-eyebrow">Categories</span>
                        <h2 class="mt-2 section-title">Browse by category</h2>
                    </div>
                    <a href="{{ route('products.index') }}" class="hidden text-sm font-semibold text-brand-600 hover:text-brand-700 sm:block">View all &rarr;</a>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach ($categories as $category)
                        <a href="{{ route('categories.show', $category) }}"
                           class="group flex flex-col items-center gap-3 rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-soft transition hover:-translate-y-1 hover:border-brand-200 hover:shadow-lift">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-2xl transition group-hover:scale-110">{{ $category->icon ?: '📦' }}</span>
                            <div>
                                <p class="text-sm font-bold text-ink-900 group-hover:text-brand-600">{{ $category->name }}</p>
                                <p class="text-xs text-slate-400">{{ $category->published_products_count }} items</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Latest --}}
        @if ($latest->isNotEmpty())
            <div class="py-8">
                <x-product-carousel :products="$latest" eyebrow="Latest" title="Check out latest items"
                    subtitle="Fresh uploads added to the marketplace." :view-all="route('products.index')" />
            </div>
        @endif

        {{-- Featured / Marketplace --}}
        @if ($featured->isNotEmpty())
            <div class="py-8">
                <x-product-carousel :products="$featured" eyebrow="Featured" title="The world-leading marketplace"
                    subtitle="Hand-picked, top-quality digital products." :view-all="route('products.index')" />
            </div>
        @endif

        {{-- Best selling --}}
        @if ($bestSelling->isNotEmpty())
            <div class="py-8">
                <x-product-carousel :products="$bestSelling" eyebrow="Popular" title="Weekly best selling items"
                    subtitle="What other builders are buying right now." :view-all="route('products.index', ['sort' => 'popular'])" />
            </div>
        @endif
    </div>

    {{-- Free items band --}}
    @if ($freeItems->isNotEmpty())
        <section class="reveal mt-10 bg-gradient-to-br from-brand-600 to-indigo-600 py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8"
                 x-data="{ scroll(dir){ $refs.t.scrollBy({left: dir*$refs.t.clientWidth*0.85, behavior:'smooth'}) } }">
                <div class="mb-6 flex items-end justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">Free</span>
                        <h2 class="mt-2 font-display text-2xl font-extrabold tracking-tight text-white sm:text-3xl">Download free items</h2>
                        <p class="mt-1.5 text-sm text-brand-100">Premium quality, zero cost. Grab them while they're free.</p>
                    </div>
                    <div class="flex items-center gap-2">
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
            <div class="mb-8 flex flex-col items-center gap-3 text-center">
                <span class="section-eyebrow">Testimonials</span>
                <h2 class="section-title">What customers are saying</h2>
                <div class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-1.5 shadow-soft">
                    <x-star-rating :rating="5" size="h-4 w-4" />
                    <span class="font-display text-sm font-extrabold text-ink-900">5.0</span>
                    <span class="text-xs text-slate-400">from happy buyers</span>
                </div>
            </div>
            @php
                $testimonials = [
                    ['name' => 'Daniel R.', 'role' => 'Full-stack Developer', 'text' => 'The code quality is outstanding and saved me weeks of work. Documentation made setup a breeze.'],
                    ['name' => 'Aisha K.', 'role' => 'Startup Founder', 'text' => 'Found exactly what we needed to launch our MVP. Instant download and great support.'],
                    ['name' => 'Marco P.', 'role' => 'UI/UX Designer', 'text' => 'Beautiful UI kits and assets. Everything is clean, modern and easy to customise.'],
                ];
            @endphp
            <div class="grid gap-6 md:grid-cols-3">
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
