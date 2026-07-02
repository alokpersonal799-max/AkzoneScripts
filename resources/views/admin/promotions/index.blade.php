@extends('layouts.admin')

@section('page-title', 'Promotions')

@section('admin')
    @php
        $popupProductsJson = $products->mapWithKeys(fn ($p) => [$p->id => [
            'title' => $p->title,
            'price' => money($p->current_price),
            'img' => $p->thumbnail_url,
        ]])->toArray();
    @endphp
    <div class="mx-auto max-w-3xl"
         x-data="{
            tab: 'hero',
            mode: '{{ old('promo_mode', $mode) }}',
            popupMode: '{{ old('popup_mode', $popupMode) }}',
            annText: @js(old('announcement_text', $announcementText)),
            annType: @js(old('announcement_type', $announcementType)),
            annCoupon: @js(old('announcement_coupon', $announcementCoupon)),
            popHeading: @js(old('popup_heading', $popupHeading)),
            popMessage: @js(old('popup_message', $popupMessage)),
            popCoupon: @js(old('popup_coupon', $popupCoupon)),
            popLinkText: @js(old('popup_link_text', $popupLinkText)) || 'Browse Products',
            popProductId: '{{ old('popup_product', $popupProduct) }}',
            products: @js($popupProductsJson)
         }">

        <div class="mb-6">
            <h1 class="font-display text-2xl font-extrabold text-ink-900">Promotions</h1>
            <p class="mt-1 text-sm text-slate-500">Drive attention with a homepage hero promo, a top announcement bar, and a welcome popup.</p>
        </div>

        {{-- Live status summary --}}
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="card flex items-center gap-3 p-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $mode !== 'off' ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" /></svg>
                </span>
                <div>
                    <p class="text-xs text-slate-400">Hero promo</p>
                    <p class="text-sm font-bold text-ink-900">{{ $mode === 'off' ? 'Off' : ucfirst($mode) }}</p>
                </div>
            </div>
            <div class="card flex items-center gap-3 p-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $announcementEnabled ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535" /></svg>
                </span>
                <div>
                    <p class="text-xs text-slate-400">Announcement bar</p>
                    <p class="text-sm font-bold {{ $announcementEnabled ? 'text-emerald-600' : 'text-slate-500' }}">{{ $announcementEnabled ? 'Enabled' : 'Disabled' }}</p>
                </div>
            </div>
            <div class="card flex items-center gap-3 p-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $popupEnabled ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" /></svg>
                </span>
                <div>
                    <p class="text-xs text-slate-400">Welcome popup</p>
                    <p class="text-sm font-bold {{ $popupEnabled ? 'text-emerald-600' : 'text-slate-500' }}">{{ $popupEnabled ? 'Enabled' : 'Disabled' }}</p>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="mb-6 flex gap-1 rounded-xl border border-slate-200 bg-white p-1">
            @foreach (['hero' => 'Hero Promo', 'announcement' => 'Announcement Bar', 'popup' => 'Popup'] as $t => $label)
                <button type="button" @click="tab = '{{ $t }}'"
                        class="flex-1 rounded-lg px-3 py-2 text-sm font-semibold transition"
                        :class="tab === '{{ $t }}' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50'">{{ $label }}</button>
            @endforeach
        </div>

        {{-- ============ HERO PROMO ============ --}}
        <form method="POST" action="{{ route('admin.promotions.update') }}" x-show="tab === 'hero'" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="hero">
            <div class="space-y-6">
                <div class="card p-6">
                    <label class="label">Promotion mode</label>
                    <div class="mt-2 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ([
                            'off' => ['Off', 'Normal hero, no promotion'],
                            'products' => ['Featured products', 'Show 3–4 product cards'],
                            'message' => ['Custom message', 'Show a banner message'],
                            'countdown' => ['Countdown offer', 'Up to 2 products + timers'],
                        ] as $value => $meta)
                            <label class="flex cursor-pointer flex-col gap-1 rounded-xl border p-3 text-sm transition"
                                   :class="mode === '{{ $value }}' ? 'border-brand-500 bg-brand-50' : 'border-slate-200 hover:bg-slate-50'">
                                <span class="flex items-center gap-2">
                                    <input type="radio" name="promo_mode" value="{{ $value }}" x-model="mode" class="text-brand-600 focus:ring-brand-500/30">
                                    <span class="font-semibold text-ink-900">{{ $meta[0] }}</span>
                                </span>
                                <span class="pl-6 text-xs text-slate-400">{{ $meta[1] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Hero schedule --}}
                <div x-show="mode !== 'off'" x-cloak class="card p-6">
                    <h2 class="font-display text-lg font-bold text-ink-900">Schedule <span class="text-xs font-normal text-slate-400">(optional)</span></h2>
                    <p class="mt-1 text-sm text-slate-500">Automatically turn this promo on and off. Leave blank to run indefinitely.</p>
                    <div class="mt-4 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="promo_starts_at" class="label">Starts at</label>
                            <input id="promo_starts_at" type="datetime-local" name="promo_starts_at" value="{{ old('promo_starts_at', $promoStartsAt ? \Illuminate\Support\Carbon::parse($promoStartsAt)->format('Y-m-d\TH:i') : '') }}" class="input">
                        </div>
                        <div>
                            <label for="promo_ends_at" class="label">Ends at</label>
                            <input id="promo_ends_at" type="datetime-local" name="promo_ends_at" value="{{ old('promo_ends_at', $promoEndsAt ? \Illuminate\Support\Carbon::parse($promoEndsAt)->format('Y-m-d\TH:i') : '') }}" class="input">
                        </div>
                    </div>
                </div>

                {{-- Featured products --}}
                <div x-show="mode === 'products'" x-cloak class="card p-6">
                    <h2 class="font-display text-lg font-bold text-ink-900">Featured products</h2>
                    <div class="mt-4">
                        <label for="promo_heading" class="label">Section label</label>
                        <input id="promo_heading" name="promo_heading" type="text" value="{{ old('promo_heading', $heading) }}" class="input" placeholder="Featured picks">
                    </div>
                    <div class="mt-4">
                        <label class="label">Choose up to 4 products</label>
                        <div class="mt-1 grid max-h-72 gap-1 overflow-y-auto rounded-xl border border-slate-200 p-3 sm:grid-cols-2">
                            @forelse ($products as $product)
                                <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-slate-50">
                                    <input type="checkbox" name="promo_products[]" value="{{ $product->id }}"
                                           {{ in_array($product->id, old('promo_products', $selectedProducts)) ? 'checked' : '' }}
                                           class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                                    <span class="truncate text-slate-700">{{ $product->title }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-slate-400">No published products yet.</p>
                            @endforelse
                        </div>
                        <p class="mt-1 text-xs text-slate-400">Only the first 4 selected (in list order) are shown.</p>
                    </div>
                </div>

                {{-- Custom message --}}
                <div x-show="mode === 'message'" x-cloak class="card p-6">
                    <h2 class="font-display text-lg font-bold text-ink-900">Custom message</h2>
                    <div class="mt-4">
                        <label for="promo_message" class="label">Message</label>
                        <input id="promo_message" name="promo_message" type="text" value="{{ old('promo_message', $message) }}" class="input" placeholder="🎉 Summer Sale — 30% off all Laravel packages this week!">
                    </div>
                    <div class="mt-4">
                        <label for="promo_message_url" class="label">Button link <span class="text-slate-400">(optional)</span></label>
                        <input id="promo_message_url" name="promo_message_url" type="url" value="{{ old('promo_message_url', $messageUrl) }}" class="input" placeholder="https://...">
                    </div>
                </div>

                {{-- Countdown --}}
                <div x-show="mode === 'countdown'" x-cloak class="card p-6">
                    <h2 class="font-display text-lg font-bold text-ink-900">Countdown offers</h2>
                    <p class="mt-1 text-sm text-slate-500">Show one or two limited-time product offers with live timers. Offer #1 is required; offer #2 is optional.</p>

                    <div class="mt-5 rounded-2xl border border-slate-200 p-4">
                        <p class="mb-3 text-xs font-bold uppercase tracking-wide text-brand-600">Offer #1</p>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="promo_countdown_product" class="label">Product</label>
                                <select id="promo_countdown_product" name="promo_countdown_product" class="input">
                                    <option value="">— Select a product —</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" {{ (int) old('promo_countdown_product', $countdownProduct) === $product->id ? 'selected' : '' }}>{{ $product->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="promo_countdown_label" class="label">Label</label>
                                <input id="promo_countdown_label" name="promo_countdown_label" type="text" value="{{ old('promo_countdown_label', $countdownLabel) }}" class="input" placeholder="Limited time offer">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="promo_countdown_until" class="label">Offer ends at</label>
                            <input id="promo_countdown_until" name="promo_countdown_until" type="datetime-local" value="{{ old('promo_countdown_until', $countdownUntil ? \Illuminate\Support\Carbon::parse($countdownUntil)->format('Y-m-d\TH:i') : '') }}" class="input">
                        </div>
                    </div>

                    <div class="mt-4 rounded-2xl border border-dashed border-slate-300 p-4">
                        <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">Offer #2 <span class="font-medium normal-case text-slate-400">— optional</span></p>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="promo_countdown_product_2" class="label">Product</label>
                                <select id="promo_countdown_product_2" name="promo_countdown_product_2" class="input">
                                    <option value="">— None —</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" {{ (int) old('promo_countdown_product_2', $countdownProduct2) === $product->id ? 'selected' : '' }}>{{ $product->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="promo_countdown_label_2" class="label">Label</label>
                                <input id="promo_countdown_label_2" name="promo_countdown_label_2" type="text" value="{{ old('promo_countdown_label_2', $countdownLabel2) }}" class="input" placeholder="Flash deal">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="promo_countdown_until_2" class="label">Offer ends at</label>
                            <input id="promo_countdown_until_2" name="promo_countdown_until_2" type="datetime-local" value="{{ old('promo_countdown_until_2', $countdownUntil2 ? \Illuminate\Support\Carbon::parse($countdownUntil2)->format('Y-m-d\TH:i') : '') }}" class="input">
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-400">Each offer shows a live timer; when it hits zero it reads “Offer ended”.</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="btn-primary btn-lg">Save hero promo</button>
            </div>
        </form>

        {{-- ============ ANNOUNCEMENT BAR ============ --}}
        <form method="POST" action="{{ route('admin.promotions.update') }}" x-show="tab === 'announcement'">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="announcement">
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-display text-lg font-bold text-ink-900">Announcement bar</h2>
                        <p class="mt-1 text-sm text-slate-500">A slim bar shown at the very top of every page.</p>
                    </div>
                    <label class="flex cursor-pointer items-center gap-2 text-sm font-semibold text-ink-900">
                        <input type="checkbox" name="announcement_enabled" value="1" {{ old('announcement_enabled', $announcementEnabled) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                        Enabled
                    </label>
                </div>

                {{-- Live preview --}}
                <div class="mt-4">
                    <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-slate-400">Live preview</p>
                    <div class="flex flex-wrap items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-center text-sm font-medium text-white"
                         :class="{ 'bg-gradient-to-r from-fuchsia-600 via-brand-600 to-indigo-600': annType==='offer', 'bg-gradient-to-r from-brand-600 to-indigo-600': annType==='info', 'bg-gradient-to-r from-emerald-600 to-teal-600': annType==='success', 'bg-gradient-to-r from-amber-500 to-orange-500': annType==='warning', 'bg-gradient-to-r from-rose-600 to-red-600': annType==='alert' }">
                        <span x-text="annText || 'Your announcement message appears here…'"></span>
                        <span x-show="annCoupon" x-cloak class="rounded border border-dashed border-white/60 bg-white/15 px-2 py-0.5 font-mono text-xs font-bold" x-text="annCoupon"></span>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="announcement_text" class="label">Message</label>
                    <input id="announcement_text" name="announcement_text" type="text" x-model="annText" value="{{ old('announcement_text', $announcementText) }}" class="input" placeholder="🎉 Summer sale — up to 40% off this week only!">
                </div>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="announcement_type" class="label">Priority / style</label>
                        <select id="announcement_type" name="announcement_type" x-model="annType" class="input">
                            @foreach (['offer' => 'Offer (promotional)', 'info' => 'Info', 'success' => 'Success', 'warning' => 'Warning', 'alert' => 'Alert (high priority)'] as $value => $label)
                                <option value="{{ $value }}" {{ old('announcement_type', $announcementType) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="announcement_link" class="label">Link <span class="text-slate-400">(optional)</span></label>
                        <input id="announcement_link" name="announcement_link" type="url" value="{{ old('announcement_link', $announcementLink) }}" class="input" placeholder="Leave blank → opens the marketplace">
                    </div>
                </div>
                <p class="mt-2 text-xs text-slate-400">If no link is set, the bar links to your products/marketplace page automatically.</p>

                <datalist id="coupon-list">
                    @foreach ($coupons as $c)<option value="{{ $c }}">@endforeach
                </datalist>

                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="announcement_coupon" class="label">Coupon code <span class="text-slate-400">(optional)</span></label>
                        <input id="announcement_coupon" name="announcement_coupon" type="text" x-model="annCoupon" value="{{ old('announcement_coupon', $announcementCoupon) }}" class="input" placeholder="e.g. SUMMER30" list="coupon-list">
                        <p class="mt-1 text-xs text-slate-400">Adds a tap-to-copy code chip to the bar.</p>
                    </div>
                </div>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="announcement_starts_at" class="label">Show from <span class="text-slate-400">(optional)</span></label>
                        <input id="announcement_starts_at" name="announcement_starts_at" type="datetime-local" value="{{ old('announcement_starts_at', $announcementStartsAt ? \Illuminate\Support\Carbon::parse($announcementStartsAt)->format('Y-m-d\TH:i') : '') }}" class="input">
                    </div>
                    <div>
                        <label for="announcement_ends_at" class="label">Hide after <span class="text-slate-400">(optional)</span></label>
                        <input id="announcement_ends_at" name="announcement_ends_at" type="datetime-local" value="{{ old('announcement_ends_at', $announcementEndsAt ? \Illuminate\Support\Carbon::parse($announcementEndsAt)->format('Y-m-d\TH:i') : '') }}" class="input">
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="btn-primary btn-lg">Save announcement bar</button>
            </div>
        </form>

        {{-- ============ POPUP ============ --}}
        <form method="POST" action="{{ route('admin.promotions.update') }}" x-show="tab === 'popup'">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="popup">
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-display text-lg font-bold text-ink-900">Promotional Popup</h2>
                        <p class="mt-1 text-sm text-slate-500">A popup card shown to visitors when they land on the site.</p>
                    </div>
                    <label class="flex cursor-pointer items-center gap-2 text-sm font-semibold text-ink-900">
                        <input type="checkbox" name="popup_enabled" value="1" {{ old('popup_enabled', $popupEnabled) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                        Enabled
                    </label>
                </div>

                {{-- Live preview --}}
                <div class="mt-5">
                    <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-slate-400">Live preview</p>
                    <div class="mx-auto max-w-xs overflow-hidden rounded-2xl border border-slate-200 bg-white text-center shadow-sm">
                        <div class="h-1.5 bg-gradient-to-r from-brand-500 via-indigo-500 to-fuchsia-500"></div>
                        <div class="p-5">
                            {{-- Message mode --}}
                            <template x-if="popupMode === 'message'">
                                <div>
                                    <h3 class="text-lg font-bold text-ink-900" x-text="popHeading || 'Your heading'"></h3>
                                    <p class="mt-2 text-sm text-slate-500" x-text="popMessage || 'Your message text shows here…'"></p>
                                </div>
                            </template>
                            {{-- Product / Offer mode --}}
                            <template x-if="popupMode !== 'message'">
                                <div>
                                    <template x-if="popupMode === 'offer'">
                                        <h3 class="text-base font-bold text-rose-600" x-text="popHeading || 'Limited Time Offer!'"></h3>
                                    </template>
                                    <img x-show="products[popProductId]" :src="products[popProductId] ? products[popProductId].img : ''" class="mx-auto mt-2 h-24 w-24 rounded-xl object-cover" alt="">
                                    <h4 class="mt-2 text-base font-bold text-ink-900" x-text="products[popProductId] ? products[popProductId].title : 'Select a product'"></h4>
                                    <p class="text-sm font-semibold text-brand-600" x-text="products[popProductId] ? products[popProductId].price : ''"></p>
                                    <p class="mt-1 text-sm text-slate-500" x-text="popMessage"></p>
                                    <template x-if="popupMode === 'offer'">
                                        <p class="mt-2 text-xs font-semibold text-rose-500">⏳ Countdown timer shows here</p>
                                    </template>
                                </div>
                            </template>
                            {{-- Coupon (all modes) --}}
                            <span x-show="popCoupon" x-cloak class="mt-3 inline-block rounded-lg border-2 border-dashed border-brand-300 bg-brand-50 px-3 py-1 font-mono text-sm font-bold text-brand-700" x-text="popCoupon"></span>
                            <div class="mt-3"><span class="btn-primary btn-sm" x-text="popupMode === 'message' ? popLinkText : (popupMode === 'offer' ? 'Grab This Deal' : 'View Product')"></span></div>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <label class="label">Popup mode</label>
                    <div class="mt-2 grid gap-3 sm:grid-cols-3">
                        @foreach ([
                            'message' => ['Custom Message', 'Heading + message + link button'],
                            'product' => ['Featured Product', 'Show a product card with view button'],
                            'offer'   => ['Product Offer', 'Product + countdown timer + urgency'],
                        ] as $pVal => $pMeta)
                            <label class="flex cursor-pointer flex-col gap-1 rounded-xl border p-3 text-sm transition"
                                   :class="popupMode === '{{ $pVal }}' ? 'border-brand-500 bg-brand-50' : 'border-slate-200 hover:bg-slate-50'">
                                <span class="flex items-center gap-2">
                                    <input type="radio" name="popup_mode" value="{{ $pVal }}" x-model="popupMode" class="text-brand-600 focus:ring-brand-500/30">
                                    <span class="font-semibold text-ink-900">{{ $pMeta[0] }}</span>
                                </span>
                                <span class="pl-6 text-xs text-slate-400">{{ $pMeta[1] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div x-show="popupMode === 'message'" x-cloak class="mt-5 space-y-4 rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-brand-600">Message fields</p>
                    <div>
                        <label for="popup_heading" class="label">Heading</label>
                        <input id="popup_heading" name="popup_heading_msg" type="text" x-model="popHeading" value="{{ old('popup_heading', $popupHeading) }}" class="input" placeholder="Welcome!">
                    </div>
                    <div>
                        <label for="popup_message_msg" class="label">Message</label>
                        <textarea id="popup_message_msg" name="popup_message_msg" rows="3" x-model="popMessage" class="input" placeholder="Check out our latest products...">{{ old('popup_message', $popupMessage) }}</textarea>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="popup_link" class="label">Link URL <span class="text-slate-400">(optional)</span></label>
                            <input id="popup_link" name="popup_link" type="url" value="{{ old('popup_link', $popupLink) }}" class="input" placeholder="https://...">
                        </div>
                        <div>
                            <label for="popup_link_text" class="label">Button text</label>
                            <input id="popup_link_text" name="popup_link_text" type="text" x-model="popLinkText" value="{{ old('popup_link_text', $popupLinkText) }}" class="input" placeholder="Browse Products">
                        </div>
                    </div>
                </div>

                <div x-show="popupMode === 'product'" x-cloak class="mt-5 space-y-4 rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-brand-600">Product fields</p>
                    <div>
                        <label for="popup_product_select" class="label">Select product</label>
                        <select id="popup_product_select" name="popup_product_prod" x-model="popProductId" class="input">
                            <option value="">-- Select a product --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ (int) old('popup_product', $popupProduct) === $product->id ? 'selected' : '' }}>{{ $product->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="popup_message_prod" class="label">Optional message</label>
                        <textarea id="popup_message_prod" name="popup_message_prod" rows="2" x-model="popMessage" class="input" placeholder="Check this out...">{{ old('popup_message', $popupMessage) }}</textarea>
                    </div>
                </div>

                <div x-show="popupMode === 'offer'" x-cloak class="mt-5 space-y-4 rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-brand-600">Offer fields</p>
                    <div>
                        <label for="popup_product_offer" class="label">Select product</label>
                        <select id="popup_product_offer" name="popup_product_off" x-model="popProductId" class="input">
                            <option value="">-- Select a product --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ (int) old('popup_product', $popupProduct) === $product->id ? 'selected' : '' }}>{{ $product->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="popup_timer_until" class="label">Countdown ends at</label>
                        <input id="popup_timer_until" name="popup_timer_until" type="datetime-local" value="{{ old('popup_timer_until', $popupTimerUntil ? \Illuminate\Support\Carbon::parse($popupTimerUntil)->format('Y-m-d\TH:i') : '') }}" class="input">
                    </div>
                    <div>
                        <label for="popup_heading_offer" class="label">Heading <span class="text-slate-400">(optional)</span></label>
                        <input id="popup_heading_offer" name="popup_heading_off" type="text" x-model="popHeading" value="{{ old('popup_heading', $popupHeading) }}" class="input" placeholder="Limited Time Offer!">
                    </div>
                    <div>
                        <label for="popup_message_offer" class="label">Message <span class="text-slate-400">(optional)</span></label>
                        <textarea id="popup_message_offer" name="popup_message_off" rows="2" x-model="popMessage" class="input" placeholder="Grab this deal before time runs out...">{{ old('popup_message', $popupMessage) }}</textarea>
                    </div>
                </div>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="popup_auto_close_seconds" class="label">Auto-close after (seconds)</label>
                        <input id="popup_auto_close_seconds" name="popup_auto_close_seconds" type="number" min="0" max="120" value="{{ old('popup_auto_close_seconds', $popupAutoCloseSeconds) }}" class="input" placeholder="0 = no auto-close">
                        <p class="mt-1 text-xs text-slate-400">Set to 0 to disable auto-close. Default is 8 seconds.</p>
                    </div>
                    <div>
                        <label class="label">Display frequency</label>
                        <div class="mt-2 space-y-2">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="popup_frequency" value="once" {{ old('popup_frequency', $popupFrequency) === 'once' ? 'checked' : '' }} class="text-brand-600 focus:ring-brand-500/30">
                                <span class="text-slate-700">Once per visitor</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="popup_frequency" value="always" {{ old('popup_frequency', $popupFrequency) === 'always' ? 'checked' : '' }} class="text-brand-600 focus:ring-brand-500/30">
                                <span class="text-slate-700">Every page load</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="popup_coupon" class="label">Coupon code <span class="text-slate-400">(optional)</span></label>
                        <input id="popup_coupon" name="popup_coupon" type="text" x-model="popCoupon" value="{{ old('popup_coupon', $popupCoupon) }}" class="input" placeholder="e.g. WELCOME10" list="coupon-list">
                        <p class="mt-1 text-xs text-slate-400">Shows a tap-to-copy code inside the popup.</p>
                    </div>
                    <div>
                        <label for="popup_audience" class="label">Show to</label>
                        <select id="popup_audience" name="popup_audience" class="input">
                            <option value="all" {{ old('popup_audience', $popupAudience) === 'all' ? 'selected' : '' }}>Everyone</option>
                            <option value="new" {{ old('popup_audience', $popupAudience) === 'new' ? 'selected' : '' }}>New visitors only</option>
                            <option value="guests" {{ old('popup_audience', $popupAudience) === 'guests' ? 'selected' : '' }}>Logged-out visitors only</option>
                        </select>
                        <p class="mt-1 text-xs text-slate-400">Target first-time or signed-out visitors.</p>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="btn-primary btn-lg">Save popup</button>
            </div>
        </form>
    </div>
@endsection
