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

        // Apply admin-configured SMTP settings at runtime (if enabled).
        $this->applyMailSettings();

        // Share data needed by every view (navbar, footer, prices).
        View::composer('*', function ($view): void {
            $cart = session('cart', []);

            $view->with([
                'cartItemCount' => is_array($cart) ? count($cart) : 0,
                'siteSettings' => \App\Models\Setting::cached(),
                'activeCurrencies' => Currency::active(),
                'currentCurrency' => app(CurrencyService::class)->current(),
            ]);
        });

        // Admin notifications for the bell menu.
        View::composer('layouts.admin', function ($view): void {
            $unread = collect();
            if (\Illuminate\Support\Facades\Schema::hasTable('admin_notifications')) {
                $unread = \App\Models\AdminNotification::whereNull('read_at')->latest()->take(10)->get();
            }
            $view->with(['adminNotifications' => $unread]);
        });
    }

    /**
     * Override mail config from the database settings when SMTP is enabled.
     */
    protected function applyMailSettings(): void
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        if (\App\Models\Setting::get('mail_enabled') !== '1' || ! \App\Models\Setting::get('mail_host')) {
            return;
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => \App\Models\Setting::get('mail_host'),
            'mail.mailers.smtp.port' => (int) \App\Models\Setting::get('mail_port', 587),
            'mail.mailers.smtp.username' => \App\Models\Setting::get('mail_username'),
            'mail.mailers.smtp.password' => \App\Models\Setting::get('mail_password'),
            'mail.mailers.smtp.encryption' => \App\Models\Setting::get('mail_encryption') ?: null,
            'mail.from.address' => \App\Models\Setting::get('mail_from_address') ?: config('mail.from.address'),
            'mail.from.name' => \App\Models\Setting::get('mail_from_name') ?: \App\Models\Setting::get('site_name', config('app.name')),
        ]);
    }
}
