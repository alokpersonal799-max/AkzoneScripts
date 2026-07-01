@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="font-display text-3xl font-extrabold tracking-tight text-ink-900">
            {{ $activeCategory?->name ?? 'Marketplace' }}
        </h1>
        <p class="mt-2 text-slate-500">
            {{ $activeCategory?->description ?? 'Browse our full catalog of premium scripts, code and design assets.' }}
        </p>
    </div>

    <div class="grid gap-8 lg:grid-cols-[260px_1fr]">
        {{-- Filters sidebar --}}
        <aside class="space-y-6">
            <form method="GET" action="{{ route('products.index') }}" class="card p-5"
                  x-data="{ more: window.matchMedia('(min-width: 1024px)').matches }"
                  @resize.window="if (window.matchMedia('(min-width: 1024px)').matches) more = true">
                <div>
                    <label class="mb-2 block text-sm font-bold text-ink-900">Search</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Keywords..."
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10">
                </div>

                {{-- More filters toggle (mobile only) --}}
                <button type="button" @click="more = !more"
                        class="mt-4 flex w-full items-center justify-between rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 lg:hidden">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" /></svg>
                        <span x-text="more ? 'Hide filters' : 'More filters'">More filters</span>
                    </span>
                    <svg class="h-4 w-4 transition-transform" :class="more && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </button>

                <div x-show="more" x-cloak class="mt-6 space-y-6">
                    <div>
                        <label class="mb-2 block text-sm font-bold text-ink-900">Category</label>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 text-sm text-slate-600">
                                <input type="radio" name="category" value="" {{ empty($filters['category']) ? 'checked' : '' }} class="border-slate-300 text-brand-600 focus:ring-brand-500/30">
                                All categories
                            </label>
                            @foreach ($categories as $category)
                                <label class="flex items-center justify-between gap-2 text-sm text-slate-600">
                                    <span class="flex items-center gap-2">
                                        <input type="radio" name="category" value="{{ $category->slug }}" {{ ($filters['category'] ?? '') === $category->slug ? 'checked' : '' }} class="border-slate-300 text-brand-600 focus:ring-brand-500/30">
                                        {{ $category->name }}
                                    </span>
                                    <span class="text-xs text-slate-400">{{ $category->published_products_count }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-ink-900">Price range</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min" min="0" step="0.01" value="{{ $filters['min'] ?? '' }}" placeholder="Min"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10">
                            <span class="text-slate-400">—</span>
                            <input type="number" name="max" min="0" step="0.01" value="{{ $filters['max'] ?? '' }}" placeholder="Max"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10">
                        </div>
                    </div>

                    <input type="hidden" name="sort" value="{{ $filters['sort'] ?? '' }}">

                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary btn-sm flex-1">Apply</button>
                        <a href="{{ route('products.index') }}" class="btn-ghost btn-sm">Reset</a>
                    </div>
                </div>
            </form>
        </aside>

        {{-- Products grid --}}
        <div>
            <div class="mb-6 flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
                <p class="text-sm text-slate-500">{{ $products->total() }} {{ Str::plural('result', $products->total()) }}</p>
                <form method="GET" action="{{ route('products.index') }}" class="flex items-center gap-2">
                    @foreach (['q', 'category', 'min', 'max'] as $hidden)
                        @if (! empty($filters[$hidden]))
                            <input type="hidden" name="{{ $hidden }}" value="{{ $filters[$hidden] }}">
                        @endif
                    @endforeach
                    <label class="text-sm text-slate-500">Sort by</label>
                    <select name="sort" onchange="this.form.submit()"
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10">
                        <option value="" {{ empty($filters['sort']) ? 'selected' : '' }}>Newest</option>
                        <option value="popular" {{ ($filters['sort'] ?? '') === 'popular' ? 'selected' : '' }}>Most popular</option>
                        <option value="rating" {{ ($filters['sort'] ?? '') === 'rating' ? 'selected' : '' }}>Top rated</option>
                        <option value="price_low" {{ ($filters['sort'] ?? '') === 'price_low' ? 'selected' : '' }}>Price: low to high</option>
                        <option value="price_high" {{ ($filters['sort'] ?? '') === 'price_high' ? 'selected' : '' }}>Price: high to low</option>
                    </select>
                </form>
            </div>

            @if ($products->isEmpty())
                <x-empty-state title="No products found" message="Try adjusting your filters or search for something else.">
                    <x-slot:action>
                        <a href="{{ route('products.index') }}" class="btn-primary btn-md">Clear filters</a>
                    </x-slot:action>
                </x-empty-state>
            @else
                <div class="grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-3">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@include('partials.ads', ['page' => 'marketplace'])
@endsection
