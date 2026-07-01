@extends('layouts.admin')

@section('page-title', 'Promotions')

@section('admin')
    <div class="mx-auto max-w-3xl" x-data="{ mode: '{{ old('promo_mode', $mode) }}', popupMode: '{{ old('popup_mode', $popupMode) }}' }">
        <div class="mb-6">
            <h1 class="font-display text-2xl font-extrabold text-ink-900">Hero Promotion</h1>
            <p class="mt-1 text-sm text-slate-500">Control the promotional band shown at the top of the homepage hero. Choose one mode below.</p>
        </div>

        <form method="POST" action="{{ route('admin.promotions.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Announcement bar (independent of hero mode) --}}
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

                <div class="mt-4">
                    <label for="announcement_text" class="label">Message</label>
                    <input id="announcement_text" name="announcement_text" type="text" value="{{ old('announcement_text', $announcementText) }}" class="input" placeholder="🎉 Summer sale — up to 40% off this week only!">
                </div>

                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="announcement_type" class="label">Priority / style</label>
                        <select id="announcement_type" name="announcement_type" class="input">
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
            </div>

            {{-- Promotional Popup --}}
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

                {{-- Message mode fields --}}
                <div x-show="popupMode === 'message'" x-cloak class="mt-5 space-y-4 rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-brand-600">Message fields</p>
                    <div>
                        <label for="popup_heading" class="label">Heading</label>
                        <input id="popup_heading" name="popup_heading" type="text" value="{{ old('popup_heading', $popupHeading) }}" class="input" placeholder="Welcome!">
                    </div>
                    <div>
                        <label for="popup_message_msg" class="label">Message</label>
                        <textarea id="popup_message_msg" name="popup_message" rows="3" class="input" placeholder="Check out our latest products...">{{ old('popup_message', $popupMessage) }}</textarea>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="popup_link" class="label">Link URL <span class="text-slate-400">(optional)</span></label>
                            <input id="popup_link" name="popup_link" type="url" value="{{ old('popup_link', $popupLink) }}" class="input" placeholder="https://...">
                        </div>
                        <div>
                            <label for="popup_link_text" class="label">Button text</label>
                            <input id="popup_link_text" name="popup_link_text" type="text" value="{{ old('popup_link_text', $popupLinkText) }}" class="input" placeholder="Browse Products">
                        </div>
                    </div>
                </div>

                {{-- Product mode fields --}}
                <div x-show="popupMode === 'product'" x-cloak class="mt-5 space-y-4 rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-brand-600">Product fields</p>
                    <div>
                        <label for="popup_product_select" class="label">Select product</label>
                        <select id="popup_product_select" name="popup_product" class="input">
                            <option value="">-- Select a product --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ (int) old('popup_product', $popupProduct) === $product->id ? 'selected' : '' }}>{{ $product->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="popup_message_prod" class="label">Optional message</label>
                        <textarea id="popup_message_prod" name="popup_message" rows="2" class="input" placeholder="Check this out...">{{ old('popup_message', $popupMessage) }}</textarea>
                    </div>
                </div>

                {{-- Offer mode fields --}}
                <div x-show="popupMode === 'offer'" x-cloak class="mt-5 space-y-4 rounded-2xl border border-slate-200 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-brand-600">Offer fields</p>
                    <div>
                        <label for="popup_product_offer" class="label">Select product</label>
                        <select id="popup_product_offer" name="popup_product" class="input">
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
                        <input id="popup_heading_offer" name="popup_heading" type="text" value="{{ old('popup_heading', $popupHeading) }}" class="input" placeholder="Limited Time Offer!">
                    </div>
                    <div>
                        <label for="popup_message_offer" class="label">Message <span class="text-slate-400">(optional)</span></label>
                        <textarea id="popup_message_offer" name="popup_message" rows="2" class="input" placeholder="Grab this deal before time runs out...">{{ old('popup_message', $popupMessage) }}</textarea>
                    </div>
                </div>

                {{-- Common popup settings --}}
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
            </div>

            {{-- Mode picker --}}
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

                {{-- Offer #1 --}}
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

                {{-- Offer #2 (optional) --}}
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

            <div class="flex justify-end">
                <button type="submit" class="btn-primary btn-lg">Save promotion</button>
            </div>
        </form>
    </div>
@endsection
