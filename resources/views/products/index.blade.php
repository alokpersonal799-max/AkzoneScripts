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
            <form method="GET" action="{{ route('products.index') }}" class="card space-y-6 p-5">
                <div>
                    <label class="mb-2 block text-sm font-bold text-ink-900">Search</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Keywords..."
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10">
                </div>

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
@endsection
