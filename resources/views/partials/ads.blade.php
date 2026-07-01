{{--
    Advertisement banner partial.
    Usage: @include('partials.ads', ['page' => 'marketplace'])

    Renders an in-flow banner section at the bottom of a page. Never fixed/overlay
    so it can't block the UI. Priority: AdSense > Meta code > manual ads.
--}}
@php
    $page = $page ?? '';
    $adsOn = setting('ads_enabled', '0') === '1' && setting('ads_page_'.$page, '1') === '1';
@endphp

@if ($adsOn)
    @php
        $adsenseCode = setting('ads_adsense_code');
        $metaCode = setting('ads_meta_code');
        $cols = (int) setting('ads_layout', 4);
        $cols = in_array($cols, [1, 2, 3, 4, 6, 8], true) ? $cols : 4;
        $ads = \App\Models\Advertisement::where('is_active', true)->orderBy('sort_order')->get();
        // Responsive grid + image height per chosen layout.
        $gridClasses = [
            1 => 'grid-cols-1',
            2 => 'grid-cols-1 sm:grid-cols-2',
            3 => 'grid-cols-2 sm:grid-cols-3',
            4 => 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-4',
            6 => 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-6',
            8 => 'grid-cols-2 sm:grid-cols-4 lg:grid-cols-8',
        ];
        $imgHeight = $cols === 1 ? 'h-40 sm:h-52' : ($cols <= 3 ? 'h-32' : 'h-28');
    @endphp

    @if (! empty($adsenseCode) || ! empty($metaCode) || $ads->isNotEmpty())
        <section class="mx-auto mt-10 max-w-7xl px-4 pb-12 sm:px-6 lg:px-8" aria-label="Advertisement">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="mb-4 text-center text-[11px] font-semibold uppercase tracking-widest text-slate-400">Advertisement</p>

                @if (! empty($adsenseCode))
                    <div class="flex justify-center">{!! $adsenseCode !!}</div>
                @elseif (! empty($metaCode))
                    <div class="flex justify-center">{!! $metaCode !!}</div>
                @else
                    <div class="grid gap-4 {{ $gridClasses[$cols] }}">
                        @foreach ($ads as $ad)
                            <a href="{{ $ad->link_url ?: '#' }}" target="_blank" rel="noopener sponsored"
                               class="group block overflow-hidden rounded-xl border border-slate-100 transition hover:shadow-soft">
                                <img src="{{ $ad->display_image }}" alt="{{ $ad->title ?: 'Advertisement' }}"
                                     class="{{ $imgHeight }} w-full object-cover transition group-hover:opacity-90">
                                @if ($ad->title)
                                    <p class="truncate px-2 py-1.5 text-center text-xs font-medium text-slate-500">{{ $ad->title }}</p>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif
@endif
