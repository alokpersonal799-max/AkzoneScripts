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
