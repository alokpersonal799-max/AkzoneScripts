@extends('layouts.app')

@section('hero')
    <section class="relative overflow-hidden">
        <div class="hero-grid absolute inset-0 opacity-60"></div>
        <div class="absolute -right-32 -top-32 h-96 w-96 rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="absolute -left-32 top-40 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl px-4 pb-20 pt-16 sm:px-6 lg:px-8 lg:pt-24">
            <div class="mx-auto max-w-3xl text-center">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1.5 text-sm font-medium text-brand-300">
                    <span class="flex h-2 w-2 rounded-full bg-brand-400"></span>
                    {{ number_format($stats['products']) }}+ premium digital products
                </span>
                <h1 class="mt-6 font-display text-4xl font-extrabold leading-tight tracking-tight text-white sm:text-6xl">
                    Premium <span class="gradient-text">scripts, code</span> &amp; design for builders
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-slate-400">
                    {{ config('marketplace.tagline') }} Download instantly, ship faster, and get lifetime access to every purchase.
                </p>

                <form action="{{ route('products.index') }}" method="GET" class="mx-auto mt-8 flex max-w-xl items-center gap-2 rounded-2xl border border-white/10 bg-ink-800/80 p-2 backdrop-blur">
                    <svg class="ml-3 h-5 w-5 flex-shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                    <input type="text" name="q" placeholder="Search scripts, templates, UI kits..." class="w-full border-0 bg-transparent py-2 text-white placeholder-slate-500 focus:ring-0">
                    <button type="submit" class="rounded-xl bg-brand-400 px-6 py-2.5 font-semibold text-ink-900 transition hover:bg-brand-300">Search</button>
                </form>

                <div class="mt-10 flex flex-wrap items-center justify-center gap-x-10 gap-y-4">
                    <div class="text-center">
                        <p class="font-display text-3xl font-bold text-white">{{ number_format($stats['products']) }}</p>
                        <p class="text-sm text-slate-500">Products</p>
                    </div>
                    <div class="hidden h-10 w-px bg-white/10 sm:block"></div>
                    <div class="text-center">
                        <p class="font-display text-3xl font-bold text-white">{{ number_format($stats['downloads']) }}</p>
                        <p class="text-sm text-slate-500">Downloads</p>
                    </div>
                    <div class="hidden h-10 w-px bg-white/10 sm:block"></div>
                    <div class="text-center">
                        <p class="font-display text-3xl font-bold text-white">{{ number_format($stats['categories']) }}</p>
                        <p class="text-sm text-slate-500">Categories</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    {{-- Categories --}}
    @if ($categories->isNotEmpty())
        <section id="categories" class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="font-display text-2xl font-bold text-white">Browse by category</h2>
                    <p class="mt-1 text-sm text-slate-400">Find exactly what your next project needs.</p>
                </div>
                <a href="{{ route('products.index') }}" class="hidden text-sm font-medium text-brand-300 hover:text-brand-200 sm:block">View all &rarr;</a>
            </div>
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($categories as $category)
                    <a href="{{ route('categories.show', $category) }}"
                       class="group flex items-center gap-4 rounded-2xl border border-white/5 bg-ink-800 p-5 transition hover:border-brand-400/30 hover:bg-ink-700">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-brand-400/20 to-indigo-500/20 text-2xl">
                            {{ $category->icon ?: '📦' }}
                        </span>
                        <div>
                            <p class="font-semibold text-white transition group-hover:text-brand-300">{{ $category->name }}</p>
                            <p class="text-xs text-slate-500">{{ $category->published_products_count }} items</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Featured --}}
    @if ($featured->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="font-display text-2xl font-bold text-white">Featured products</h2>
                    <p class="mt-1 text-sm text-slate-400">Hand-picked by our team.</p>
                </div>
                <a href="{{ route('products.index') }}" class="hidden text-sm font-medium text-brand-300 hover:text-brand-200 sm:block">Explore all &rarr;</a>
            </div>
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featured as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Latest --}}
    @if ($latest->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <h2 class="font-display text-2xl font-bold text-white">Fresh arrivals</h2>
                <a href="{{ route('products.index') }}" class="text-sm font-medium text-brand-300 hover:text-brand-200">View all &rarr;</a>
            </div>
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($latest as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- CTA banner --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-ink-800 to-ink-700 p-8 sm:p-14">
            <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-brand-500/20 blur-3xl"></div>
            <div class="relative max-w-2xl">
                <h2 class="font-display text-3xl font-bold text-white sm:text-4xl">Ready to ship your next big idea?</h2>
                <p class="mt-4 text-lg text-slate-400">Join thousands of developers and designers who save weeks of work with battle-tested code and assets.</p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('products.index') }}" class="rounded-xl bg-brand-400 px-6 py-3 font-semibold text-ink-900 transition hover:bg-brand-300">Browse marketplace</a>
                    @guest
                        <a href="{{ route('register') }}" class="rounded-xl border border-white/15 px-6 py-3 font-semibold text-white transition hover:bg-white/5">Create free account</a>
                    @endguest
                </div>
            </div>
        </div>
    </section>
@endsection
