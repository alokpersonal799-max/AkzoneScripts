<?php

namespace App\Providers;

use App\Models\Currency;
use App\Models\Product;
use App\Observers\ProductObserver;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // One instance per request so the chosen currency is remembered.
        $this->app->singleton(CurrencyService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Automatically generate a slug whenever a product is saved.
        Product::observe(ProductObserver::class);

        // Share data needed by every view (navbar, footer, prices).
        View::composer('*', function ($view): void {
            $cart = session('cart', []);

            $view->with([
                'cartItemCount' => is_array($cart) ? count($cart) : 0,
                'siteSettings' => \App\Models\Setting::all(),
                'activeCurrencies' => Currency::active(),
                'currentCurrency' => app(CurrencyService::class)->current(),
            ]);
        });
    }
}
