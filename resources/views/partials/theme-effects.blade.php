@php
    $themeKey = active_theme();
    $fx = config('themes.'.$themeKey.'.effect', 'none');
@endphp

@if (in_array($fx, ['confetti', 'snow', 'hearts'], true))
    @php
        $glyphs = match ($fx) {
            'snow' => ['❄', '❅', '❆', '•'],
            'hearts' => ['❤', '💕', '💖', '💗', '💝'],
            default => ['🎉', '🎊', '✨', '🎈', '⭐'],
        };
    @endphp
    <div class="theme-fx pointer-events-none fixed inset-0 z-40 overflow-hidden" aria-hidden="true">
        @for ($i = 0; $i < 26; $i++)
            <span class="theme-fx-item"
                  style="left: {{ random_int(0, 100) }}%; animation-duration: {{ random_int(6, 15) }}s; animation-delay: -{{ random_int(0, 14) }}s; font-size: {{ random_int(12, 28) }}px; opacity: {{ random_int(45, 95) / 100 }};">{{ $glyphs[array_rand($glyphs)] }}</span>
        @endfor
    </div>
@elseif ($fx === 'ribbon')
    <div class="pointer-events-none fixed top-0 right-0 z-40 h-32 w-32 overflow-hidden" aria-hidden="true">
        <div class="theme-ribbon">SALE</div>
    </div>
@endif
