<?php

use App\Models\Setting;
use App\Services\CurrencyService;

if (! function_exists('setting')) {
    /**
     * Get a site setting value, or all settings when no key is given.
     */
    function setting(?string $key = null, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return Setting::cached();
        }

        return Setting::get($key, $default);
    }
}

if (! function_exists('money')) {
    /**
     * Format a base-currency amount in the visitor's selected currency.
     */
    function money(float|int|string|null $amount): string
    {
        return app(CurrencyService::class)->format((float) $amount);
    }
}

if (! function_exists('currency_symbol')) {
    /**
     * The symbol of the visitor's selected currency.
     */
    function currency_symbol(): string
    {
        return app(CurrencyService::class)->symbol();
    }
}

if (! function_exists('base_symbol')) {
    /**
     * The symbol of the store's base (default) currency — used in admin/historical amounts.
     */
    function base_symbol(): string
    {
        return \App\Models\Currency::default()?->symbol ?? config('marketplace.currency_symbol', '$');
    }
}


if (! function_exists('active_theme')) {
    /**
     * The currently effective store theme key.
     *
     * Respects a scheduled theme override: if scheduling is enabled and the
     * current time falls within the configured window, the scheduled theme
     * wins; otherwise the manually selected theme is used.
     */
    function active_theme(): string
    {
        $themes = array_keys(config('themes', []));
        $selected = setting('active_theme', 'default');

        try {
            if (setting('theme_schedule_enabled', '0') === '1') {
                $scheduled = setting('theme_schedule_theme');
                $start = setting('theme_schedule_start');
                $end = setting('theme_schedule_end');

                if ($scheduled && $start && $end && in_array($scheduled, $themes, true)) {
                    if (now()->between(\Illuminate\Support\Carbon::parse($start), \Illuminate\Support\Carbon::parse($end))) {
                        return $scheduled;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Fall through to the manually selected theme on any parse error.
        }

        return in_array($selected, $themes, true) ? $selected : 'default';
    }
}


if (! function_exists('country_name')) {
    /**
     * Resolve a country display name from an ISO 3166-1 alpha-2 code.
     */
    function country_name(?string $code): string
    {
        if (! $code) {
            return 'Unknown';
        }

        $code = strtoupper($code);

        return config('countries.'.$code, $code);
    }
}

if (! function_exists('country_flag')) {
    /**
     * Convert an ISO 3166-1 alpha-2 code into a flag emoji (🇮🇳, 🇺🇸, ...).
     */
    function country_flag(?string $code): string
    {
        $code = strtoupper((string) $code);

        if (strlen($code) !== 2 || ! ctype_alpha($code)) {
            return '🏳️';
        }

        $flag = '';
        foreach (str_split($code) as $char) {
            $flag .= mb_chr(0x1F1E6 + (ord($char) - ord('A')), 'UTF-8');
        }

        return $flag;
    }
}


if (! function_exists('schedule_active')) {
    /**
     * Whether "now" falls within an optional start/end window.
     * Empty bounds are treated as open-ended.
     */
    function schedule_active(?string $startsAt, ?string $endsAt): bool
    {
        try {
            if (! empty($startsAt) && now()->lt(\Illuminate\Support\Carbon::parse($startsAt))) {
                return false;
            }
            if (! empty($endsAt) && now()->gt(\Illuminate\Support\Carbon::parse($endsAt))) {
                return false;
            }
        } catch (\Throwable $e) {
            return true;
        }

        return true;
    }
}
