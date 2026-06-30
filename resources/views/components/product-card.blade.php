@props(['product'])

<article {{ $attributes->merge(['class' => 'group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-soft transition duration-300 hover:-translate-y-1.5 hover:border-brand-200 hover:shadow-lift']) }}>
    <a href="{{ route('products.show', $product) }}" class="relative block aspect-[16/10] overflow-hidden bg-slate-100">
        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}"
             class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
        <div class="absolute inset-x-0 top-0 flex items-start justify-between p-3">
            @if ($product->is_featured)
                <span class="chip bg-indigo-600/95 text-white shadow-sm backdrop-blur">Featured</span>
            @else
                <span></span>
            @endif
            @if ($product->is_on_sale)
                <span class="chip bg-rose-500/95 text-white shadow-sm backdrop-blur">Sale</span>
            @endif
        </div>
    </a>

    <div class="flex flex-1 flex-col p-4">
        <div class="flex items-center justify-between gap-2">
            <span class="chip bg-brand-50 text-brand-700">{{ $product->category?->name ?? 'Uncategorized' }}</span>
            <x-star-rating :rating="$product->rating" :count="$product->reviews_count" size="h-3.5 w-3.5" />
        </div>

        <h3 class="mt-3 font-display text-base font-bold leading-snug text-ink-900">
            <a href="{{ route('products.show', $product) }}" class="transition hover:text-brand-600">{{ $product->title }}</a>
        </h3>
        <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $product->tagline ?: Str::limit(strip_tags($product->description), 80) }}</p>

        <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
            <div class="flex items-baseline gap-1.5">
                <x-price :amount="$product->current_price" class="font-display text-lg font-extrabold text-ink-900" />
                @if ($product->is_on_sale)
                    <span class="text-sm text-slate-400 line-through">{{ config('marketplace.currency_symbol') }}{{ number_format($product->price, 2) }}</span>
                @endif
            </div>
            <form method="POST" action="{{ route('cart.add', $product) }}">
                @csrf
                <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition hover:bg-brand-600 hover:text-white" title="Add to cart">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                </button>
            </form>
        </div>
    </div>
</article>
