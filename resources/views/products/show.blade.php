@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex flex-wrap items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-brand-600">Marketplace</a>
        <span>/</span>
        <a href="{{ route('categories.show', $product->category) }}" class="hover:text-brand-600">{{ $product->category->name }}</a>
        <span>/</span>
        <span class="text-ink-900">{{ Str::limit($product->title, 30) }}</span>
    </nav>

    <div class="grid gap-10 lg:grid-cols-[1.4fr_1fr]">
        {{-- Left: media + details --}}
        <div>
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-soft">
                <img src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}" class="aspect-[16/9] w-full object-cover">
            </div>

            <div class="card mt-8 p-6 sm:p-8">
                <h2 class="font-display text-xl font-bold text-ink-900">About this product</h2>
                <div class="mt-4 whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ $product->description }}</div>

                @if ($product->tags && count($product->tags))
                    <div class="mt-6 flex flex-wrap gap-2">
                        @foreach ($product->tags as $tag)
                            <span class="chip bg-slate-100 text-slate-600">#{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Reviews --}}
            <div class="card mt-8 p-6 sm:p-8">
                <div class="flex items-center justify-between">
                    <h2 class="font-display text-xl font-bold text-ink-900">Reviews</h2>
                    <x-star-rating :rating="$product->rating" :count="$product->reviews_count" size="h-5 w-5" />
                </div>

                @auth
                    @if ($canReview)
                        <form method="POST" action="{{ route('reviews.store', $product) }}" class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5"
                              x-data="{ rating: 5 }">
                            @csrf
                            <p class="text-sm font-semibold text-ink-900">Leave a review</p>
                            <div class="mt-3 flex items-center gap-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" @click="rating = {{ $i }}" class="focus:outline-none">
                                        <svg class="h-7 w-7 transition" :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-slate-300'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z" /></svg>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" :value="rating">
                            <textarea name="comment" rows="3" placeholder="Share your experience..."
                                      class="mt-3 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10"></textarea>
                            <button type="submit" class="btn-primary btn-sm mt-3">Submit review</button>
                        </form>
                    @endif
                @endauth

                <div class="mt-6 space-y-5">
                    @forelse ($product->approvedReviews as $review)
                        <div class="border-t border-slate-100 pt-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-indigo-500 text-sm font-bold text-white">{{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}</span>
                                    <div>
                                        <p class="text-sm font-bold text-ink-900">{{ $review->user->name ?? 'Anonymous' }}</p>
                                        <p class="text-xs text-slate-400">{{ $review->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <x-star-rating :rating="$review->rating" size="h-4 w-4" />
                            </div>
                            @if ($review->comment)
                                <p class="mt-3 text-sm text-slate-600">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="border-t border-slate-100 pt-5 text-sm text-slate-500">No reviews yet. Be the first to share your thoughts.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right: purchase panel --}}
        <div class="lg:sticky lg:top-24 lg:self-start">
            <div class="card p-6 sm:p-8">
                <div class="flex items-center gap-2">
                    <span class="chip bg-brand-50 text-brand-700">{{ $product->category->name }}</span>
                    @if ($product->is_featured)
                        <span class="chip bg-indigo-50 text-indigo-700">Featured</span>
                    @endif
                </div>
                <h1 class="mt-4 font-display text-2xl font-extrabold leading-tight text-ink-900">{{ $product->title }}</h1>
                @if ($product->tagline)
                    <p class="mt-2 text-slate-500">{{ $product->tagline }}</p>
                @endif

                <div class="mt-6 flex items-baseline gap-3">
                    <x-price :amount="$product->current_price" class="font-display text-4xl font-extrabold text-ink-900" />
                    @if ($product->is_on_sale)
                        <span class="text-lg text-slate-400 line-through">{{ money($product->price) }}</span>
                    @endif
                </div>

                <div class="mt-6 space-y-3">
                    @if ($hasPurchased)
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm font-semibold text-emerald-700">
                            ✓ You own this product
                        </div>
                        <a href="{{ route('dashboard.purchases') }}" class="btn-primary btn-lg w-full">Go to downloads</a>
                    @else
                        <form method="POST" action="{{ route('cart.add', $product) }}">
                            @csrf
                            <button type="submit" class="btn-primary btn-lg w-full">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272" /></svg>
                                Add to cart
                            </button>
                        </form>
                    @endif

                    @auth
                        <form method="POST" action="{{ route('wishlist.toggle', $product) }}">
                            @csrf
                            <button type="submit" class="btn-ghost btn-lg w-full">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg>
                                Add to wishlist
                            </button>
                        </form>
                    @endauth

                    @if ($product->demo_url)
                        <a href="{{ $product->demo_url }}" target="_blank" rel="noopener" class="btn-ghost btn-lg w-full">
                            Live preview
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                        </a>
                    @endif
                </div>

                {{-- Meta --}}
                <dl class="mt-8 space-y-3 border-t border-slate-100 pt-6 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Version</dt><dd class="font-semibold text-ink-900">{{ $product->version }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">File size</dt><dd class="font-semibold text-ink-900">{{ $product->formatted_file_size }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Downloads</dt><dd class="font-semibold text-ink-900">{{ number_format($product->downloads) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Last updated</dt><dd class="font-semibold text-ink-900">{{ $product->updated_at->format('M j, Y') }}</dd></div>
                </dl>

                @auth
                    <div x-data="{ open: false }" class="mt-6 border-t border-slate-100 pt-4 text-center">
                        <button @click="open = true" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 hover:text-rose-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" /></svg>
                            Report this product
                        </button>

                        {{-- Modal --}}
                        <div x-show="open" x-cloak @keydown.escape.window="open = false" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                            <div @click="open = false" class="absolute inset-0 bg-slate-900/40"></div>
                            <div class="relative w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 text-left shadow-2xl">
                                <h3 class="font-display text-lg font-bold text-ink-900">Report product</h3>
                                <p class="mt-1 text-sm text-slate-500">Let us know what's wrong with this product.</p>
                                <form method="POST" action="{{ route('reports.store', $product) }}" class="mt-4 space-y-3">
                                    @csrf
                                    <select name="reason" required class="input">
                                        <option value="">Select a reason</option>
                                        <option>Copyright / stolen content</option>
                                        <option>Broken or malicious code</option>
                                        <option>Misleading description</option>
                                        <option>Spam or scam</option>
                                        <option>Other</option>
                                    </select>
                                    <textarea name="details" rows="3" class="input" placeholder="Add any details (optional)"></textarea>
                                    <div class="flex justify-end gap-2">
                                        <button type="button" @click="open = false" class="btn-ghost btn-sm">Cancel</button>
                                        <button type="submit" class="btn-primary btn-sm">Submit report</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    {{-- Related --}}
    @if ($related->isNotEmpty())
        <section class="mt-16">
            <h2 class="section-title">You might also like</h2>
            <div class="mt-6 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                @foreach ($related as $item)
                    <x-product-card :product="$item" />
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
