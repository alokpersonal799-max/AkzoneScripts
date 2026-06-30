@extends('layouts.admin')

@section('page-title', 'Promotions')

@section('admin')
    <div class="mx-auto max-w-3xl" x-data="{ mode: '{{ old('promo_mode', $mode) }}' }">
        <div class="mb-6">
            <h1 class="font-display text-2xl font-extrabold text-ink-900">Hero Promotion</h1>
            <p class="mt-1 text-sm text-slate-500">Control the promotional band shown at the top of the homepage hero. Choose one mode below.</p>
        </div>

        <form method="POST" action="{{ route('admin.promotions.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Mode picker --}}
            <div class="card p-6">
                <label class="label">Promotion mode</label>
                <div class="mt-2 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        'off' => ['Off', 'Normal hero, no promotion'],
                        'products' => ['Featured products', 'Show 3–4 product cards'],
                        'message' => ['Custom message', 'Show a banner message'],
                        'countdown' => ['Countdown offer', 'One product + timer'],
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
                <h2 class="font-display text-lg font-bold text-ink-900">Countdown offer</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
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
                    <p class="mt-1 text-xs text-slate-400">A live countdown timer is shown; when it hits zero the band reads “Offer ended”.</p>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary btn-lg">Save promotion</button>
            </div>
        </form>
    </div>
@endsection
