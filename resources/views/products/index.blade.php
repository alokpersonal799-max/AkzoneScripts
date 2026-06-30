@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="font-display text-3xl font-bold text-white">
            {{ $activeCategory?->name ?? 'Marketplace' }}
        </h1>
        <p class="mt-2 text-slate-400">
            {{ $activeCategory?->description ?? 'Browse our full catalog of premium scripts, code and design assets.' }}
        </p>
    </div>

    <div class="grid gap-8 lg:grid-cols-[260px_1fr]">
        {{-- Filters sidebar --}}
        <aside class="space-y-6">
            <form method="GET" action="{{ route('products.index') }}" class="space-y-6 rounded-2xl border border-white/5 bg-ink-800 p-5">
                <div>
                    <label class="block text-sm font-semibold text-white">Search</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Keywords..."
                           class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-white">Category</label>
                    <div class="mt-2 space-y-1">
                        <label class="flex items-center gap-2 text-sm text-slate-300">
                            <input type="radio" name="category" value="" {{ empty($filters['category']) ? 'checked' : '' }} class="border-white/20 bg-ink-900 text-brand-500 focus:ring-brand-400/30">
                            All categories
                        </label>
                        @foreach ($categories as $category)
                            <label class="flex items-center justify-between gap-2 text-sm text-slate-300">
                                <span class="flex items-center gap-2">
                                    <input type="radio" name="category" value="{{ $category->slug }}" {{ ($filters['category'] ?? '') === $category->slug ? 'checked' : '' }} class="border-white/20 bg-ink-900 text-brand-500 focus:ring-brand-400/30">
                                    {{ $category->name }}
                                </span>
                                <span class="text-xs text-slate-500">{{ $category->published_products_count }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-white">Price range</label>
                    <div class="mt-2 flex items-center gap-2">
                        <input type="number" name="min" min="0" step="0.01" value="{{ $filters['min'] ?? '' }}" placeholder="Min"
                               class="w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                        <span class="text-slate-500">—</span>
                        <input type="number" name="max" min="0" step="0.01" value="{{ $filters['max'] ?? '' }}" placeholder="Max"
                               class="w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                    </div>
                </div>

                <input type="hidden" name="sort" value="{{ $filters['sort'] ?? '' }}">

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 rounded-lg bg-brand-400 px-4 py-2 text-sm font-semibold text-ink-900 transition hover:bg-brand-300">Apply</button>
                    <a href="{{ route('products.index') }}" class="rounded-lg border border-white/10 px-4 py-2 text-sm font-medium text-slate-300 transition hover:bg-white/5">Reset</a>
                </div>
            </form>
        </aside>

        {{-- Products grid --}}
        <div>
            <div class="mb-6 flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
                <p class="text-sm text-slate-400">{{ $products->total() }} {{ Str::plural('result', $products->total()) }}</p>
                <form method="GET" action="{{ route('products.index') }}" class="flex items-center gap-2">
                    @foreach (['q', 'category', 'min', 'max'] as $hidden)
                        @if (! empty($filters[$hidden]))
                            <input type="hidden" name="{{ $hidden }}" value="{{ $filters[$hidden] }}">
                        @endif
                    @endforeach
                    <label class="text-sm text-slate-400">Sort by</label>
                    <select name="sort" onchange="this.form.submit()"
                            class="rounded-lg border border-white/10 bg-ink-800 px-3 py-2 text-sm text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
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
                        <a href="{{ route('products.index') }}" class="rounded-lg bg-brand-400 px-5 py-2.5 text-sm font-semibold text-ink-900 hover:bg-brand-300">Clear filters</a>
                    </x-slot:action>
                </x-empty-state>
            @else
                <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
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
@endsection
