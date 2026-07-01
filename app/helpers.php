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
