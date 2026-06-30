@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center gap-2 text-sm text-slate-400">
        <a href="{{ route('home') }}" class="hover:text-brand-300">Home</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-brand-300">Marketplace</a>
        <span>/</span>
        <a href="{{ route('categories.show', $product->category) }}" class="hover:text-brand-300">{{ $product->category->name }}</a>
        <span>/</span>
        <span class="text-slate-200">{{ Str::limit($product->title, 30) }}</span>
    </nav>

    <div class="grid gap-10 lg:grid-cols-[1.4fr_1fr]">
        {{-- Left: media + details --}}
        <div>
            <div class="overflow-hidden rounded-2xl border border-white/5 bg-ink-800">
                <img src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}" class="aspect-[16/9] w-full object-cover">
            </div>

            <div class="mt-8 rounded-2xl border border-white/5 bg-ink-800 p-6 sm:p-8">
                <h2 class="font-display text-xl font-bold text-white">About this product</h2>
                <div class="prose prose-invert mt-4 max-w-none text-slate-300">
                    {!! nl2br(e($product->description)) !!}
                </div>

                @if ($product->tags && count($product->tags))
                    <div class="mt-6 flex flex-wrap gap-2">
                        @foreach ($product->tags as $tag)
                            <span class="rounded-full bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">#{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Reviews --}}
            <div class="mt-8 rounded-2xl border border-white/5 bg-ink-800 p-6 sm:p-8">
                <div class="flex items-center justify-between">
                    <h2 class="font-display text-xl font-bold text-white">Reviews</h2>
                    <x-star-rating :rating="$product->rating" :count="$product->reviews_count" size="h-5 w-5" />
                </div>

                @auth
                    @if ($canReview)
                        <form method="POST" action="{{ route('reviews.store', $product) }}" class="mt-6 rounded-xl border border-white/10 bg-ink-900 p-5"
                              x-data="{ rating: 5 }">
                            @csrf
                            <p class="text-sm font-medium text-white">Leave a review</p>
                            <div class="mt-3 flex items-center gap-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" @click="rating = {{ $i }}" class="focus:outline-none">
                                        <svg class="h-7 w-7 transition" :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-slate-600'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z" /></svg>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" :value="rating">
                            <textarea name="comment" rows="3" placeholder="Share your experience..."
                                      class="mt-3 w-full rounded-lg border border-white/10 bg-ink-800 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20"></textarea>
                            <button type="submit" class="mt-3 rounded-lg bg-brand-400 px-5 py-2 text-sm font-semibold text-ink-900 hover:bg-brand-300">Submit review</button>
                        </form>
                    @endif
                @endauth

                <div class="mt-6 space-y-5">
                    @forelse ($product->approvedReviews as $review)
                        <div class="border-t border-white/5 pt-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-brand-400 to-indigo-500 text-sm font-bold text-ink-900">{{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}</span>
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ $review->user->name ?? 'Anonymous' }}</p>
                                        <p class="text-xs text-slate-500">{{ $review->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <x-star-rating :rating="$review->rating" size="h-4 w-4" />
                            </div>
                            @if ($review->comment)
                                <p class="mt-3 text-sm text-slate-300">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="border-t border-white/5 pt-5 text-sm text-slate-400">No reviews yet. Be the first to share your thoughts.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right: purchase panel --}}
        <div class="lg:sticky lg:top-24 lg:self-start">
            <div class="rounded-2xl border border-white/5 bg-ink-800 p-6 sm:p-8">
                <div class="flex items-center gap-2">
                    <span class="rounded-md bg-white/5 px-2.5 py-1 text-xs font-medium text-brand-300">{{ $product->category->name }}</span>
                    @if ($product->is_featured)
                        <span class="rounded-md bg-indigo-500/20 px-2.5 py-1 text-xs font-medium text-indigo-300">Featured</span>
                    @endif
                </div>
                <h1 class="mt-4 font-display text-2xl font-bold leading-tight text-white">{{ $product->title }}</h1>
                @if ($product->tagline)
                    <p class="mt-2 text-slate-400">{{ $product->tagline }}</p>
                @endif

                <div class="mt-6 flex items-baseline gap-3">
                    <x-price :amount="$product->current_price" class="font-display text-4xl font-extrabold text-white" />
                    @if ($product->is_on_sale)
                        <span class="text-lg text-slate-500 line-through">{{ config('marketplace.currency_symbol') }}{{ number_format($product->price, 2) }}</span>
                    @endif
                </div>

                <div class="mt-6 space-y-3">
                    @if ($hasPurchased)
                        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-center text-sm font-medium text-emerald-300">
                            You own this product
                        </div>
                        <a href="{{ route('dashboard.purchases') }}" class="block w-full rounded-xl bg-brand-400 px-4 py-3 text-center font-semibold text-ink-900 transition hover:bg-brand-300">
                            Go to downloads
                        </a>
                    @else
                        <form method="POST" action="{{ route('cart.add', $product) }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-400 px-4 py-3 font-semibold text-ink-900 transition hover:bg-brand-300">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272" /></svg>
                                Add to cart
                            </button>
                        </form>
                    @endif

                    @auth
                        <form method="POST" action="{{ route('wishlist.toggle', $product) }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-white/10 px-4 py-3 font-medium text-slate-300 transition hover:bg-white/5">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg>
                                Add to wishlist
                            </button>
                        </form>
                    @endauth

                    @if ($product->demo_url)
                        <a href="{{ $product->demo_url }}" target="_blank" rel="noopener" class="flex w-full items-center justify-center gap-2 rounded-xl border border-white/10 px-4 py-3 font-medium text-slate-300 transition hover:bg-white/5">
                            Live preview
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                        </a>
                    @endif
                </div>

                {{-- Meta --}}
                <dl class="mt-8 space-y-3 border-t border-white/5 pt-6 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-400">Version</dt><dd class="font-medium text-white">{{ $product->version }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">File size</dt><dd class="font-medium text-white">{{ $product->formatted_file_size }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">Downloads</dt><dd class="font-medium text-white">{{ number_format($product->downloads) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">Last updated</dt><dd class="font-medium text-white">{{ $product->updated_at->format('M j, Y') }}</dd></div>
                </dl>
            </div>
        </div>
    </div>

    {{-- Related --}}
    @if ($related->isNotEmpty())
        <section class="mt-16">
            <h2 class="font-display text-2xl font-bold text-white">You might also like</h2>
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($related as $item)
                    <x-product-card :product="$item" />
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
