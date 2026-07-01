{{--
    Promotional Popup - Configurable popup card shown to site visitors.
    Modes: message, product, offer
    Frequency: once (localStorage) or always (every page load)
    Auto-close: configurable seconds (0 = disabled)
--}}
@php
    $popupMode = setting('popup_mode', 'message');
    $popupHeading = setting('popup_heading', '');
    $popupMessage = setting('popup_message', '');
    $popupLink = setting('popup_link', '');
    $popupLinkText = setting('popup_link_text', 'Learn More');
    $popupTimerUntil = setting('popup_timer_until', '');
    $popupAutoClose = (int) setting('popup_auto_close_seconds', 8);
    $popupFrequency = setting('popup_frequency', 'once');
    $popupProductId = (int) setting('popup_product', 0);
    $popupProduct = null;

    if (($popupMode === 'product' || $popupMode === 'offer') && $popupProductId) {
        $popupProduct = \Illuminate\Support\Facades\Cache::remember(
            'popup_product_' . $popupProductId,
            300, // 5 minutes
            function () use ($popupProductId) {
                return \App\Models\Product::where('id', $popupProductId)->where('status', 'published')->first();
            }
        );
    }

    // Build a content hash for localStorage key (changing content resets "once" tracking)
    $popupHash = substr(md5($popupMode . $popupHeading . $popupMessage . $popupProductId . $popupTimerUntil), 0, 10);
@endphp

<div x-data="{
        open: false,
        timer: null,
        autoClose: {{ $popupAutoClose }},
        frequency: '{{ $popupFrequency }}',
        storageKey: 'akz_popup_{{ $popupHash }}',
        init() {
            if (this.frequency === 'once') {
                if (localStorage.getItem(this.storageKey) === '1') return;
            }
            this.$nextTick(() => { this.open = true; });
            if (this.autoClose > 0) {
                this.timer = setTimeout(() => { this.close(); }, this.autoClose * 1000);
            }
        },
        close() {
            this.open = false;
            if (this.timer) clearTimeout(this.timer);
            if (this.frequency === 'once') {
                localStorage.setItem(this.storageKey, '1');
            }
        }
     }"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4">

    {{-- Overlay --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="close()"
         class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    {{-- Popup Card --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-90 translate-y-4"
         class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">

        {{-- Gradient accent top bar --}}
        <div class="h-1.5 bg-gradient-to-r from-brand-500 via-indigo-500 to-fuchsia-500"></div>

        {{-- Close button --}}
        <button @click="close()" type="button"
                class="absolute right-3 top-4 z-10 rounded-full p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="p-6">
            @if ($popupMode === 'message')
                {{-- Message Mode --}}
                <div class="text-center">
                    @if ($popupHeading)
                        <h3 class="text-xl font-bold text-ink-900">{{ $popupHeading }}</h3>
                    @endif
                    @if ($popupMessage)
                        <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $popupMessage }}</p>
                    @endif
                    @if ($popupLink && $popupLinkText)
                        <a href="{{ $popupLink }}" class="btn-primary btn-md mt-5 inline-flex">{{ $popupLinkText }}</a>
                    @endif
                </div>
            @elseif ($popupMode === 'product' && $popupProduct)
                {{-- Product Mode --}}
                <div class="text-center">
                    @if ($popupProduct->thumbnail_url)
                        <img src="{{ $popupProduct->thumbnail_url }}" alt="{{ $popupProduct->title }}" class="mx-auto h-32 w-32 rounded-xl object-cover shadow-sm">
                    @endif
                    <h3 class="mt-4 text-lg font-bold text-ink-900">{{ $popupProduct->title }}</h3>
                    <p class="mt-1 text-lg font-semibold text-brand-600">{{ $popupProduct->current_price }}</p>
                    @if ($popupMessage)
                        <p class="mt-2 text-sm text-slate-500">{{ $popupMessage }}</p>
                    @endif
                    <a href="{{ route('products.show', $popupProduct) }}" class="btn-primary btn-md mt-4 inline-flex">View Product</a>
                </div>
            @elseif ($popupMode === 'offer' && $popupProduct)
                {{-- Offer Mode with countdown --}}
                <div class="text-center">
                    @if ($popupHeading)
                        <h3 class="text-xl font-bold text-rose-600">{{ $popupHeading }}</h3>
                    @else
                        <h3 class="text-xl font-bold text-rose-600">Limited Time Offer!</h3>
                    @endif

                    @if ($popupProduct->thumbnail_url)
                        <img src="{{ $popupProduct->thumbnail_url }}" alt="{{ $popupProduct->title }}" class="mx-auto mt-3 h-28 w-28 rounded-xl object-cover shadow-sm">
                    @endif
                    <h4 class="mt-3 text-lg font-bold text-ink-900">{{ $popupProduct->title }}</h4>
                    <p class="mt-1 text-lg font-semibold text-brand-600">{{ $popupProduct->current_price }}</p>

                    @if ($popupMessage)
                        <p class="mt-2 text-sm text-slate-500">{{ $popupMessage }}</p>
                    @endif

                    {{-- Countdown timer --}}
                    @if ($popupTimerUntil)
                        <div x-data="{
                                target: new Date('{{ \Illuminate\Support\Carbon::parse($popupTimerUntil)->toIso8601String() }}').getTime(),
                                days: 0, hours: 0, minutes: 0, seconds: 0,
                                expired: false,
                                tick() {
                                    const now = Date.now();
                                    const diff = this.target - now;
                                    if (diff <= 0) { this.expired = true; return; }
                                    this.days = Math.floor(diff / 86400000);
                                    this.hours = Math.floor((diff % 86400000) / 3600000);
                                    this.minutes = Math.floor((diff % 3600000) / 60000);
                                    this.seconds = Math.floor((diff % 60000) / 1000);
                                },
                                init() { this.tick(); setInterval(() => this.tick(), 1000); }
                             }" class="mt-4">
                            <template x-if="!expired">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="rounded-lg bg-rose-50 px-3 py-2 text-center">
                                        <span x-text="days" class="block text-lg font-bold text-rose-600"></span>
                                        <span class="text-[10px] font-medium uppercase text-rose-400">Days</span>
                                    </div>
                                    <span class="text-lg font-bold text-slate-300">:</span>
                                    <div class="rounded-lg bg-rose-50 px-3 py-2 text-center">
                                        <span x-text="hours" class="block text-lg font-bold text-rose-600"></span>
                                        <span class="text-[10px] font-medium uppercase text-rose-400">Hrs</span>
                                    </div>
                                    <span class="text-lg font-bold text-slate-300">:</span>
                                    <div class="rounded-lg bg-rose-50 px-3 py-2 text-center">
                                        <span x-text="minutes" class="block text-lg font-bold text-rose-600"></span>
                                        <span class="text-[10px] font-medium uppercase text-rose-400">Min</span>
                                    </div>
                                    <span class="text-lg font-bold text-slate-300">:</span>
                                    <div class="rounded-lg bg-rose-50 px-3 py-2 text-center">
                                        <span x-text="seconds" class="block text-lg font-bold text-rose-600"></span>
                                        <span class="text-[10px] font-medium uppercase text-rose-400">Sec</span>
                                    </div>
                                </div>
                            </template>
                            <template x-if="expired">
                                <p class="text-sm font-semibold text-slate-400">Offer has ended</p>
                            </template>
                        </div>
                    @endif

                    <a href="{{ route('products.show', $popupProduct) }}" class="btn-primary btn-md mt-4 inline-flex">Grab This Deal</a>
                </div>
            @endif
        </div>

        {{-- Auto-close progress bar --}}
        @if ($popupAutoClose > 0)
            <div class="h-1 w-full bg-slate-100">
                <div class="h-1 bg-brand-500 transition-all ease-linear"
                     x-ref="progressBar"
                     x-init="$nextTick(() => { if ($refs.progressBar) { $refs.progressBar.style.width = '100%'; $refs.progressBar.style.transitionDuration = '{{ $popupAutoClose }}s'; setTimeout(() => { $refs.progressBar.style.width = '0%'; }, 50); } })"></div>
            </div>
        @endif
    </div>
</div>
