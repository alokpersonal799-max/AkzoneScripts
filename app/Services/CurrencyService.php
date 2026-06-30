<?php

namespace App\Services;

use App\Models\Currency;

class CurrencyService
{
    public const SESSION_KEY = 'currency_code';

    protected ?Currency $current = null;

    /**
     * The currency the visitor is currently browsing in.
     */
    public function current(): ?Currency
    {
        if ($this->current) {
            return $this->current;
        }

        $code = session(self::SESSION_KEY);
        $active = Currency::active();

        $this->current = ($code ? $active->firstWhere('code', $code) : null)
            ?? Currency::default();

        return $this->current;
    }

    /**
     * Switch the active currency (by code) for the session.
     */
    public function switch(string $code): void
    {
        if (Currency::active()->firstWhere('code', $code)) {
            session([self::SESSION_KEY => $code]);
            $this->current = null;
        }
    }

    /**
     * Convert a base-currency amount into the active currency.
     */
    public function convert(float $amount): float
    {
        $rate = (float) ($this->current()?->exchange_rate ?? 1);

        return $amount * $rate;
    }

    /**
     * Format a base-currency amount for display in the active currency.
     */
    public function format(float $amount): string
    {
        $symbol = $this->current()?->symbol ?? '$';

        return $symbol.number_format($this->convert($amount), 2);
    }

    /**
     * The symbol of the active currency.
     */
    public function symbol(): string
    {
        return $this->current()?->symbol ?? '$';
    }
}
