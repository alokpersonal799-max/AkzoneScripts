<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Automatically generate a slug whenever a product is saved.
        Product::observe(ProductObserver::class);

        // Share the live cart item count with every view so the navbar badge
        // always reflects the current session cart.
        View::composer('*', function ($view): void {
            $cart = session('cart', []);
            $view->with('cartItemCount', is_array($cart) ? count($cart) : 0);
        });
    }
}
