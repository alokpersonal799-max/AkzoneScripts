@props(['product'])

<article {{ $attributes->merge(['class' => 'group relative flex flex-col overflow-hidden rounded-2xl border border-white/5 bg-ink-800 transition duration-300 hover:-translate-y-1 hover:border-brand-400/30 hover:shadow-glow']) }}>
    <a href="{{ route('products.show', $product) }}" class="relative block aspect-[16/10] overflow-hidden bg-ink-700">
        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}"
             class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
        <div class="absolute inset-x-0 top-0 flex items-start justify-between p-3">
            @if ($product->is_featured)
                <span class="rounded-full bg-indigo-500/90 px-2.5 py-1 text-xs font-semibold text-white backdrop-blur">Featured</span>
            @else
                <span></span>
            @endif
            @if ($product->is_on_sale)
                <span class="rounded-full bg-rose-500/90 px-2.5 py-1 text-xs font-semibold text-white backdrop-blur">Sale</span>
            @endif
        </div>
    </a>

    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-center gap-2">
            <span class="rounded-md bg-white/5 px-2 py-0.5 text-xs font-medium text-brand-300">{{ $product->category?->name ?? 'Uncategorized' }}</span>
            <x-star-rating :rating="$product->rating" :count="$product->reviews_count" size="h-3.5 w-3.5" />
        </div>

        <h3 class="mt-3 font-display text-lg font-semibold leading-snug text-white">
            <a href="{{ route('products.show', $product) }}" class="transition hover:text-brand-300">{{ $product->title }}</a>
        </h3>
        <p class="mt-1 line-clamp-2 text-sm text-slate-400">{{ $product->tagline ?: Str::limit(strip_tags($product->description), 80) }}</p>

        <div class="mt-4 flex items-center gap-2 text-xs text-slate-500">
            <span class="inline-flex items-center gap-1">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" /></svg>
                {{ number_format($product->downloads) }}
            </span>
        </div>

        <div class="mt-4 flex items-center justify-between border-t border-white/5 pt-4">
            <div class="flex items-baseline gap-2">
                <x-price :amount="$product->current_price" class="font-display text-xl font-bold text-white" />
                @if ($product->is_on_sale)
                    <span class="text-sm text-slate-500 line-through">{{ config('marketplace.currency_symbol') }}{{ number_format($product->price, 2) }}</span>
                @endif
            </div>
            <form method="POST" action="{{ route('cart.add', $product) }}">
                @csrf
                <button type="submit" class="rounded-lg bg-white/5 p-2.5 text-brand-300 transition hover:bg-brand-400 hover:text-ink-900" title="Add to cart">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                </button>
            </form>
        </div>
    </div>
</article>
