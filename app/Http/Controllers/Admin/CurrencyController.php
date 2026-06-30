<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    public function index(): View
    {
        return view('admin.currencies.index', [
            'currencies' => Currency::orderByDesc('is_default')->orderBy('code')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateCurrency($request);
        $this->persist($data);

        return back()->with('success', 'Currency added.');
    }

    public function update(Request $request, Currency $currency): RedirectResponse
    {
        $data = $this->validateCurrency($request, $currency);
        $this->persist($data, $currency);

        return back()->with('success', 'Currency updated.');
    }

    public function destroy(Currency $currency): RedirectResponse
    {
        if ($currency->is_default) {
            return back()->with('error', 'You cannot delete the default currency.');
        }

        $currency->delete();

        return back()->with('success', 'Currency removed.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateCurrency(Request $request, ?Currency $currency = null): array
    {
        $codeRule = 'unique:currencies,code'.($currency ? ','.$currency->id : '');

        return $request->validate([
            'code' => ['required', 'string', 'max:8', $codeRule],
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['required', 'string', 'max:8'],
            'exchange_rate' => ['required', 'numeric', 'min:0.000001'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function persist(array $data, ?Currency $currency = null): void
    {
        $data['code'] = strtoupper($data['code']);
        $isDefault = ! empty($data['is_default']);
        $data['is_default'] = $isDefault;
        $data['is_active'] = ! empty($data['is_active']) || $isDefault;

        // The default currency is always the base (rate 1).
        if ($isDefault) {
            $data['exchange_rate'] = 1;
            Currency::query()->update(['is_default' => false]);
        }

        if ($currency) {
            $currency->update($data);
        } else {
            Currency::create($data);
        }

        // Guarantee at least one default currency exists.
        if (! Currency::where('is_default', true)->exists()) {
            Currency::query()->orderBy('id')->first()?->update(['is_default' => true, 'exchange_rate' => 1, 'is_active' => true]);
        }
    }
}
