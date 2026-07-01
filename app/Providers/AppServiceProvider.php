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

        // Audit trail: log create/update/delete on key models.
        foreach ([
            \App\Models\Product::class,
            \App\Models\Category::class,
            \App\Models\Service::class,
            \App\Models\Coupon::class,
            \App\Models\Page::class,
            \App\Models\Order::class,
            \App\Models\Advertisement::class,
            \App\Models\TelegramBot::class,
            \App\Models\Announcement::class,
        ] as $auditable) {
            $auditable::observe(\App\Observers\ActivityObserver::class);
        }

        // Apply admin-configured SMTP settings at runtime (if enabled).
        $this->applyMailSettings();

        // Point the storage disks at the admin-selected cloud provider (if any).
        $this->applyStorageSettings();

        // Apply the admin-selected timezone.
        $this->applyTimezone();

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

    /**
     * Apply the admin-configured timezone (falls back to the .env value).
     */
    protected function applyTimezone(): void
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        $tz = \App\Models\Setting::get('timezone');

        if ($tz && in_array($tz, timezone_identifiers_list(), true)) {
            config(['app.timezone' => $tz]);
            date_default_timezone_set($tz);
        }
    }

    /**
     * Re-point the "public" (images) and "products" (files) disks at the
     * admin-selected S3-compatible provider. We override the existing disk
     * names so all current code (Storage::disk('public'|'products')) works
     * unchanged. Falls back to local storage if anything is missing.
     */
    protected function applyStorageSettings(): void
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        $provider = \App\Models\Setting::get('storage_provider', 'local');

        if ($provider === 'local' || $provider === '' || $provider === null) {
            return;
        }

        $key = \App\Models\Setting::get('storage_s3_key');
        $secret = \App\Models\Setting::get('storage_s3_secret');
        $bucket = \App\Models\Setting::get('storage_s3_bucket');

        // Not fully configured, or the S3 SDK isn't installed → stay on local
        // storage so the site keeps working instead of throwing.
        if (! $key || ! $secret || ! $bucket || ! class_exists(\Aws\S3\S3Client::class)) {
            return;
        }

        $endpoint = \App\Models\Setting::get('storage_s3_endpoint') ?: null;
        $publicUrl = \App\Models\Setting::get('storage_s3_url') ?: null;

        $base = [
            'driver' => 's3',
            'key' => $key,
            'secret' => $secret,
            'region' => \App\Models\Setting::get('storage_s3_region') ?: 'us-east-1',
            'bucket' => $bucket,
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => \App\Models\Setting::get('storage_s3_path_style') === '1',
            'throw' => false,
            'report' => false,
        ];

        config([
            // Public images (thumbnails, gallery, branding) → "images/" prefix, public.
            'filesystems.disks.public' => array_merge($base, [
                'root' => 'images',
                'visibility' => 'public',
                'url' => $publicUrl,
            ]),
            // Private downloadable packages → "files/" prefix, private.
            'filesystems.disks.products' => array_merge($base, [
                'root' => 'files',
                'visibility' => 'private',
            ]),
        ]);
    }
}
