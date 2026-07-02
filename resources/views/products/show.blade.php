@extends('layouts.app')

@section('content')
@php
    $gallery = $product->gallery_urls;
    $whatsapp = $product->effective_whatsapp;
    $telegram = $product->effective_telegram;
    $ratingText = $product->reviews_count ? number_format($product->rating, 1).'/5 from '.$product->reviews_count.' reviews' : 'brand new';
    $chatMessage = "Hi! 👋\n\nI'm interested in this product:\n\n🛍️ {$product->title}\n📂 Category: {$product->category->name}\n⭐ Rating: {$ratingText}\n💰 Price: ".money($product->current_price)."\n🔗 ".request()->fullUrl()."\n\nCould you please share more details about it?";
    $waLink = $whatsapp ? 'https://wa.me/'.preg_replace('/\D/', '', $whatsapp).'?text='.rawurlencode($chatMessage) : null;
    $tgHandle = $telegram ? ltrim($telegram, '@') : null;
    $tgLink = $tgHandle ? (\Illuminate\Support\Str::startsWith($tgHandle, 'http') ? $tgHandle : 'https://t.me/'.$tgHandle) : null;
@endphp
<div class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex flex-wrap items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
        <span>/</span>
        <a href="{{ route('categories.show', $product->category) }}" class="hover:text-brand-600">{{ $product->category->name }}</a>
        <span>/</span>
        <span class="text-ink-900">{{ Str::limit($product->title, 30) }}</span>
    </nav>

    <div class="grid gap-8 lg:grid-cols-[1.4fr_1fr]">
        {{-- Left: gallery + details --}}
        <div>
            {{-- Auto-sliding gallery --}}
            <div class="card overflow-hidden p-3"
                 x-data="{
                    active: 0,
                    count: {{ count($gallery) }},
                    timer: null,
                    start() { if (this.count > 1) this.timer = setInterval(() => this.next(), 4500); },
                    stop() { clearInterval(this.timer); },
                    next() { this.active = (this.active + 1) % this.count; },
                    go(i) { this.active = i; this.stop(); this.start(); }
                 }"
                 x-init="start()" @mouseenter="stop()" @mouseleave="start()">
                <div class="relative aspect-[16/10] overflow-hidden rounded-xl bg-slate-100">
                    @foreach ($gallery as $i => $url)
                        <img src="{{ $url }}" alt="{{ $product->title }}"
                             x-show="active === {{ $i }}" x-transition.opacity.duration.500ms
                             class="absolute inset-0 h-full w-full object-cover" @if($i) x-cloak @endif>
                    @endforeach

                    @if (count($gallery) > 1)
                        <button @click="go((active - 1 + count) % count)" class="absolute left-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-slate-700 shadow hover:bg-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                        </button>
                        <button @click="go((active + 1) % count)" class="absolute right-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-slate-700 shadow hover:bg-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                        </button>
                        <div class="absolute inset-x-0 bottom-3 flex justify-center gap-1.5">
                            @foreach ($gallery as $i => $url)
                                <button @click="go({{ $i }})" class="h-2 rounded-full transition-all" :class="active === {{ $i }} ? 'w-6 bg-brand-500' : 'w-2 bg-white/80'"></button>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if (count($gallery) > 1)
                    <div class="mt-3 flex gap-2 overflow-x-auto">
                        @foreach ($gallery as $i => $url)
                            <button @click="go({{ $i }})" class="h-16 w-20 flex-none overflow-hidden rounded-lg border-2 transition" :class="active === {{ $i }} ? 'border-brand-500' : 'border-transparent'">
                                <img src="{{ $url }}" alt="" class="h-full w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Description --}}
            <div class="card mt-8 p-6 sm:p-8">
                <h2 class="flex items-center gap-2 font-display text-xl font-bold text-ink-900">
                    <svg class="h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                    Description
                </h2>
                <div class="mt-4 whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ $product->description }}</div>
                @if ($product->tags && count($product->tags))
                    <div class="mt-6 flex flex-wrap gap-2">
                        @foreach ($product->tags as $tag)
                            <span class="chip bg-slate-100 text-slate-600">#{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Changelog / Version History --}}
            @if ($product->changelogs->count())
                <div class="card mt-8 p-6 sm:p-8">
                    <h2 class="flex items-center gap-2 font-display text-xl font-bold text-ink-900">
                        <svg class="h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        Changelog
                    </h2>
                    <div class="mt-5 relative">
                        {{-- Timeline line --}}
                        <div class="absolute left-[7px] top-2 bottom-2 w-0.5 bg-slate-200"></div>

                        <div class="space-y-6">
                            @foreach ($product->changelogs as $entry)
                                <div class="relative pl-7">
                                    {{-- Timeline dot --}}
                                    <span class="absolute left-0 top-1.5 h-[15px] w-[15px] rounded-full border-2 border-brand-500 bg-white"></span>
                                    <div class="flex flex-wrap items-baseline gap-2">
                                        <span class="inline-flex items-center rounded-lg bg-brand-50 px-2 py-0.5 text-xs font-bold text-brand-700">v{{ $entry->version }}</span>
                                        <span class="text-xs text-slate-400">{{ $entry->released_at->format('M j, Y') }}</span>
                                    </div>
                                    <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $entry->notes }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Reviews --}}
            <div class="card mt-8 p-6 sm:p-8">
                <div class="flex items-center justify-between">
                    <h2 class="flex items-center gap-2 font-display text-xl font-bold text-ink-900">
                        <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z" /></svg>
                        Customer reviews
                    </h2>
                    <x-star-rating :rating="$product->rating" :count="$product->reviews_count" size="h-5 w-5" />
                </div>

                @auth
                    @if ($canReview)
                        <form method="POST" action="{{ route('reviews.store', $product) }}" class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5" x-data="{ rating: 5 }">
                            @csrf
                            <p class="text-sm font-semibold text-ink-900">Leave a review</p>
                            @if ($isFree)
                                <p class="mt-0.5 text-xs text-slate-400">This is a free product — your review won't carry a "Verified purchaser" badge.</p>
                            @endif
                            <div class="mt-3 flex items-center gap-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" @click="rating = {{ $i }}" class="focus:outline-none">
                                        <svg class="h-7 w-7 transition" :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-slate-300'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z" /></svg>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" :value="rating">
                            <textarea name="comment" rows="3" placeholder="Share your experience..." class="mt-3 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10"></textarea>
                            <button type="submit" class="btn-primary btn-sm mt-3">Submit review</button>
                            <p class="mt-2 text-xs text-slate-400">Reviews are published after admin approval.</p>
                        </form>
                    @elseif ($alreadyReviewed)
                        <p class="mt-4 flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            <svg class="h-4 w-4 flex-none" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            Thanks! You've already reviewed this product. It appears once an admin approves it.
                        </p>
                    @elseif ($mustPurchase)
                        <div class="mt-4 flex items-start gap-2 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-700">
                            <svg class="mt-0.5 h-4 w-4 flex-none" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                            <span>You can review this product only after purchasing it. Once your purchase is complete, come back here to share your feedback.</span>
                        </div>
                    @endif
                @else
                    <p class="mt-4 rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-500">
                        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">Login</a> to write a review.
                    </p>
                @endauth

                <div class="mt-6 space-y-5">
                    @forelse ($product->approvedReviews as $review)
                        <div class="border-t border-slate-100 pt-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-indigo-500 text-sm font-bold text-white">{{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}</span>
                                    <div>
                                        <p class="flex items-center gap-1.5 text-sm font-bold text-ink-900">
                                            {{ $review->user->name ?? 'Anonymous' }}
                                            @if ($review->is_verified)
                                                <span class="inline-flex items-center gap-0.5 rounded-full bg-emerald-100 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700" title="Verified purchaser">
                                                    <svg class="h-2.5 w-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                                                    Verified purchaser
                                                </span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-slate-400">{{ $review->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <x-star-rating :rating="$review->rating" size="h-4 w-4" />
                            </div>
                            @if ($review->comment)<p class="mt-3 text-sm text-slate-600">{{ $review->comment }}</p>@endif
                            @if ($review->admin_reply)
                                <div class="mt-3 ml-6 rounded-xl border-l-4 border-brand-300 bg-brand-50/60 px-4 py-3">
                                    <p class="flex items-center gap-1.5 text-xs font-bold text-brand-700">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" /></svg>
                                        {{ setting('site_name', 'Store') }} replied @if ($review->replied_at)· {{ $review->replied_at->diffForHumans() }}@endif
                                    </p>
                                    <p class="mt-1 text-sm text-slate-600">{{ $review->admin_reply }}</p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="flex flex-col items-center py-8 text-center">
                            <svg class="h-10 w-10 text-slate-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z" /></svg>
                            <p class="mt-3 text-sm text-slate-400">No reviews yet. Be the first to review!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right: purchase panel --}}
        <div class="lg:sticky lg:top-24 lg:self-start">
            <div class="card p-6 sm:p-8">
                <div class="flex items-center gap-2">
                    <span class="chip bg-brand-50 text-brand-700">{{ $product->category->name }}</span>
                    @if ($product->is_featured)<span class="chip bg-indigo-50 text-indigo-700">Featured</span>@endif
                </div>
                <h1 class="mt-4 font-display text-2xl font-extrabold leading-tight text-ink-900">{{ $product->title }}</h1>
                <p class="mt-2 flex items-center gap-2 text-sm text-slate-500">
                    By <span class="font-semibold text-brand-600">{{ setting('site_name', 'AkzoneScripts') }}</span>
                    <span class="inline-flex items-center gap-1 text-emerald-600"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>{{ $product->viewers_now }} people viewing now</span>
                </p>

                <div class="mt-5 flex flex-wrap items-baseline gap-3">
                    <x-price :amount="$product->current_price" class="font-display text-4xl font-extrabold text-ink-900" />
                    @if ($product->is_on_sale)<span class="text-lg text-slate-400 line-through">{{ money($product->price) }}</span>@endif
                    @if ($product->is_expired)
                        <span class="chip bg-rose-50 text-rose-700">Deal expired</span>
                    @elseif ($product->is_out_of_stock)
                        <span class="chip bg-rose-50 text-rose-700">Out of stock</span>
                    @elseif ($product->manages_stock)
                        <span class="chip bg-emerald-50 text-emerald-700">{{ $product->stock }} in stock</span>
                    @elseif ($product->is_purchasable || $product->is_free)
                        <span class="chip bg-emerald-50 text-emerald-700">In stock</span>
                    @else
                        <span class="chip bg-amber-50 text-amber-700">Contact to buy</span>
                    @endif
                </div>

                {{-- Limited-time deal countdown --}}
                @if ($product->is_deal && ! $product->is_expired)
                    <div class="mt-4 flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800"
                         x-data="{ end: new Date('{{ $product->deal_ends_at->toIso8601String() }}').getTime(), t: '',
                            tick() { let diff = this.end - Date.now(); if (diff <= 0) { this.t = 'Expired'; location.reload(); return; }
                                let s = Math.floor(diff/1000); let d = Math.floor(s/86400); s%=86400; let h = Math.floor(s/3600); s%=3600; let m = Math.floor(s/60); s%=60;
                                this.t = (d>0? d+'d ':'') + h+'h '+m+'m '+s+'s'; } }"
                         x-init="tick(); setInterval(() => tick(), 1000)">
                        <svg class="h-5 w-5 flex-none" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <span>Limited-time deal ends in <span x-text="t" class="font-mono font-bold text-amber-900"></span></span>
                    </div>
                @endif

                {{-- Action buttons --}}
                <div class="mt-6 space-y-3">
                    @if ($hasPurchased)
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm font-semibold text-emerald-700">✓ You own this product</div>
                        <a href="{{ route('dashboard.purchases') }}" class="btn-primary btn-lg w-full">Go to downloads</a>
                    @elseif ($product->is_expired)
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-4 text-center">
                            <p class="font-display text-lg font-bold text-rose-600">⏳ Deal expired</p>
                            <p class="mt-1 text-sm text-rose-500">This limited-time offer has ended and is no longer available for purchase.</p>
                        </div>
                    @elseif ($product->is_out_of_stock)
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-4 text-center">
                            <p class="font-display text-lg font-bold text-rose-600">Out of stock</p>
                            <p class="mt-1 text-sm text-rose-500">All units have been sold. Check back later or contact us.</p>
                        </div>
                    @elseif ($product->is_free)
                        {{-- Free product → direct download (login required), no checkout --}}
                        @auth
                            <a href="{{ route('products.free', $product) }}" class="btn btn-lg w-full bg-emerald-500 text-white hover:bg-emerald-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                Download Now — Free
                            </a>
                            <p class="text-center text-xs text-slate-400">This product is free. Click to download instantly.</p>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-lg w-full bg-emerald-500 text-white hover:bg-emerald-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                Sign in to download — Free
                            </a>
                            <p class="text-center text-xs text-slate-400">This product is free. <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">Sign in</a> or <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:underline">create an account</a> to download.</p>
                        @endauth
                    @elseif ($product->is_purchasable)
                        @if ($waLink || $tgLink)
                            <div class="grid grid-cols-2 gap-3">
                                @if ($waLink)
                                    <a href="{{ $waLink }}" target="_blank" rel="noopener" class="btn btn-lg bg-emerald-500 text-white hover:bg-emerald-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163a11.867 11.867 0 0 1-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.82 11.82 0 0 1 8.413 3.488 11.82 11.82 0 0 1 3.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 0 1-5.688-1.448L.057 24Zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884a9.86 9.86 0 0 0 1.51 5.26l-.999 3.648 3.737-.98 .242 .147Z"/></svg>
                                        WhatsApp
                                    </a>
                                @endif
                                @if ($tgLink)
                                    <a href="{{ $tgLink }}" target="_blank" rel="noopener" class="btn btn-lg bg-sky-500 text-white hover:bg-sky-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z"/></svg>
                                        Telegram
                                    </a>
                                @endif
                            </div>
                        @endif
                        <form method="POST" action="{{ route('cart.buy', $product) }}">
                            @csrf
                            <button type="submit" class="btn-dark btn-lg w-full">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" /></svg>
                                Buy Now
                            </button>
                        </form>
                        <form method="POST" action="{{ route('cart.add', $product) }}">
                            @csrf
                            <button type="submit" class="btn btn-lg w-full bg-brand-600 text-white hover:bg-brand-700">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272" /></svg>
                                Add to Cart
                            </button>
                        </form>
                    @else
                        {{-- Not available for direct sale → contact to purchase --}}
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                            This product isn't available for direct checkout. Contact us to purchase it directly:
                        </div>
                        @if ($waLink || $tgLink)
                            <div class="grid grid-cols-2 gap-3">
                                @if ($waLink)
                                    <a href="{{ $waLink }}" target="_blank" rel="noopener" class="btn btn-lg bg-emerald-500 text-white hover:bg-emerald-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163a11.867 11.867 0 0 1-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.82 11.82 0 0 1 8.413 3.488 11.82 11.82 0 0 1 3.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 0 1-5.688-1.448L.057 24Zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884a9.86 9.86 0 0 0 1.51 5.26l-.999 3.648 3.737-.98 .242 .147Z"/></svg>
                                        WhatsApp
                                    </a>
                                @endif
                                @if ($tgLink)
                                    <a href="{{ $tgLink }}" target="_blank" rel="noopener" class="btn btn-lg bg-sky-500 text-white hover:bg-sky-600">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z"/></svg>
                                        Telegram
                                    </a>
                                @endif
                            </div>
                        @endif
                        @unless ($waLink || $tgLink)
                            <p class="text-center text-xs text-slate-400">No contact channel configured yet.</p>
                        @endunless
                    @endif

                    <div class="flex gap-3" x-data="{ noPreview: false }">
                        @auth
                            <form method="POST" action="{{ route('wishlist.toggle', $product) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="btn-ghost btn-md w-full">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg>
                                    Wishlist
                                </button>
                            </form>
                        @endauth
                        @if ($product->demo_url)
                            <a href="{{ $product->demo_url }}" target="_blank" rel="noopener" class="btn-ghost btn-md flex-1">Live Preview</a>
                        @else
                            <button type="button" @click="noPreview = true" class="btn-ghost btn-md flex-1">Live Preview</button>
                        @endif

                        {{-- Preview unavailable modal --}}
                        <div x-show="noPreview" x-cloak @keydown.escape.window="noPreview = false" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                            <div @click="noPreview = false" class="absolute inset-0 bg-slate-900/40"></div>
                            <div class="relative w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-2xl">
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                                </span>
                                <h3 class="mt-4 font-display text-lg font-bold text-ink-900">Preview unavailable</h3>
                                <p class="mt-2 text-sm text-slate-500">Sorry, the platform where this preview was hosted is currently shut down. You can contact us to request a live preview.</p>
                                <div class="mt-5 flex justify-center gap-2">
                                    @if ($waLink)
                                        <a href="{{ $waLink }}" target="_blank" rel="noopener" class="btn btn-sm bg-emerald-500 text-white hover:bg-emerald-600">Request via WhatsApp</a>
                                    @endif
                                    <button type="button" @click="noPreview = false" class="btn-ghost btn-sm">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Vendor card --}}
                <div class="mt-6 flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-indigo-500 font-bold text-white">{{ strtoupper(substr(setting('site_name', 'A'), 0, 1)) }}</span>
                        <div>
                            <p class="text-xs text-slate-400">Whose product</p>
                            <p class="text-sm font-bold text-ink-900">{{ setting('site_name', 'AkzoneScripts') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('products.index') }}" class="btn-ghost btn-sm">View Store</a>
                </div>

                {{-- Guaranteed safe checkout --}}
                <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200">
                    <div class="flex items-center gap-2 bg-ink-900 px-4 py-2.5 text-sm font-semibold text-white">
                        <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                        Guaranteed Safe &amp; Secure Checkout
                    </div>
                    <div class="flex flex-wrap items-center justify-center gap-2 px-4 py-3">
                        @foreach (['VISA', 'Mastercard', 'RuPay', 'UPI', 'PayPal', 'Stripe'] as $pay)
                            <span class="rounded-md border border-slate-200 bg-white px-2.5 py-1 text-[10px] font-bold text-slate-500">{{ $pay }}</span>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-3 divide-x divide-slate-100 border-t border-slate-100 text-center text-xs text-slate-500">
                        <div class="px-2 py-3">↺<br>30-Day Support</div>
                        <div class="px-2 py-3">🛡️<br>Buyer Protection</div>
                        <div class="px-2 py-3">⚡<br>Instant Delivery</div>
                    </div>
                </div>

                {{-- Meta --}}
                <dl class="mt-6 space-y-3 border-t border-slate-100 pt-6 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Version</dt><dd class="font-semibold text-ink-900">{{ $product->version }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">File size</dt><dd class="font-semibold text-ink-900">{{ $product->formatted_file_size }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">{{ $product->is_free ? 'Downloads' : 'Sold' }}</dt><dd class="font-semibold text-ink-900">{{ $product->is_free ? number_format($product->downloads) : number_format($product->sales) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Last updated</dt><dd class="font-semibold text-ink-900">{{ $product->updated_at->format('M j, Y') }}</dd></div>
                </dl>

                @auth
                    <div x-data="{ open: false }" class="mt-6 border-t border-slate-100 pt-4 text-center">
                        <button @click="open = true" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 hover:text-rose-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" /></svg>
                            Report this product
                        </button>
                        <div x-show="open" x-cloak @keydown.escape.window="open = false" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                            <div @click="open = false" class="absolute inset-0 bg-slate-900/40"></div>
                            <div class="relative w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 text-left shadow-2xl">
                                <h3 class="font-display text-lg font-bold text-ink-900">Report product</h3>
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
            <h2 class="section-title">More from this seller</h2>
            <div class="mt-6 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                @foreach ($related as $item)
                    <x-product-card :product="$item" />
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
