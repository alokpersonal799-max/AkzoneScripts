<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Illuminate\Http\RedirectResponse;

class CurrencyController extends Controller
{
    public function __construct(protected CurrencyService $currency) {}

    /**
     * Switch the active display currency for the visitor.
     */
    public function switch(string $code): RedirectResponse
    {
        $this->currency->switch(strtoupper($code));

        return back()->with('success', 'Currency updated.');
    }
}
