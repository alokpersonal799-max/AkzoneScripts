@props(['products', 'eyebrow' => null, 'title' => '', 'subtitle' => null, 'viewAll' => null])

<section class="reveal" x-data="{
    atStart: true,
    atEnd: false,
    update() {
        const el = $refs.track;
        this.atStart = el.scrollLeft <= 4;
        this.atEnd = el.scrollLeft + el.clientWidth >= el.scrollWidth - 4;
    },
    scroll(dir) {
        $refs.track.scrollBy({ left: dir * $refs.track.clientWidth * 0.85, behavior: 'smooth' });
    }
}" x-init="update()">
    <div class="mb-6 flex items-end justify-between gap-4">
        <div>
            @if ($eyebrow)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-600">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>{{ $eyebrow }}
                </span>
            @endif
            <h2 class="mt-2 section-title">{{ $title }}</h2>
            @if ($subtitle)
                <p class="mt-1.5 text-sm text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>
        <div class="flex items-center gap-2">
            @if ($viewAll)
                <a href="{{ $viewAll }}" class="hidden rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-ink-900 transition hover:border-brand-300 hover:text-brand-600 sm:block">View All</a>
            @endif
            <button type="button" @click="scroll(-1)" :disabled="atStart" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 disabled:opacity-40">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            </button>
            <button type="button" @click="scroll(1)" :disabled="atEnd" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 disabled:opacity-40">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
            </button>
        </div>
    </div>

    <div x-ref="track" @scroll.debounce.50ms="update()" class="no-scrollbar -mx-1 flex snap-x gap-4 overflow-x-auto scroll-smooth px-1 pb-2 sm:gap-5">
        @foreach ($products as $product)
            <x-product-card :product="$product" class="w-[46%] flex-none snap-start sm:w-[290px]" />
        @endforeach
    </div>
</section>
