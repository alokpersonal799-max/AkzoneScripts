<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Seeds the essential settings and currencies the app needs to run.
 * Always run during installation (independent of demo data).
 */
class CoreSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedCurrencies();
    }

    protected function seedSettings(): void
    {
        $defaults = [
            // General
            'site_name' => ['general', 'AkzoneScripts'],
            'site_logo' => ['general', null],
            'support_email' => ['general', 'support@akzonescripts.test'],
            'social_twitter' => ['general', '#'],
            'social_github' => ['general', '#'],
            'social_discord' => ['general', '#'],
            'social_facebook' => ['general', '#'],
            'contact_whatsapp' => ['general', ''],
            'contact_telegram' => ['general', ''],

            // Hero
            'hero_badge' => ['hero', 'Trusted by thousands of builders'],
            'hero_title' => ['hero', 'curated digital products for your next project'],
            'hero_highlight' => ['hero', 'next project'],
            'hero_subtitle' => ['hero', 'Buy and download premium scripts, source code, UI kits and design assets. Instant delivery, lifetime access and updates included.'],

            // Announcement bar
            'announcement_enabled' => ['general', '1'],
            'announcement_text' => ['general', 'Limited time — get up to 30% off selected premium products.'],

            // Footer
            'footer_about' => ['footer', 'Discover a vibrant online hub offering a diverse collection of premium scripts & plugins for seamless digital products.'],
            'footer_copyright' => ['footer', '© '.date('Y').' AkzoneScripts. All rights reserved.'],

            // SEO
            'seo_title' => ['seo', ''],
            'seo_description' => ['seo', ''],
            'seo_keywords' => ['seo', ''],
            'seo_og_image' => ['seo', null],
            'analytics_id' => ['seo', ''],

            // Maintenance
            'maintenance_enabled' => ['maintenance', '0'],
            'maintenance_message' => ['maintenance', 'We are performing scheduled maintenance. Please check back shortly.'],

            // Payment gateways
            'pay_manual_enabled' => ['payments', '1'],
            'pay_stripe_enabled' => ['payments', '0'],
            'stripe_key' => ['payments', ''],
            'stripe_secret' => ['payments', ''],
            'pay_paypal_enabled' => ['payments', '0'],
            'paypal_client_id' => ['payments', ''],
            'paypal_secret' => ['payments', ''],
            'paypal_mode' => ['payments', 'sandbox'],
            'pay_razorpay_enabled' => ['payments', '0'],
            'razorpay_key' => ['payments', ''],
            'razorpay_secret' => ['payments', ''],

            // Manual payment methods
            'manual_instructions' => ['manual', 'Pay using any method below, then enter your transaction ID and upload a payment screenshot. We will verify and unlock your downloads.'],
            'manual_upi_id' => ['manual', ''],
            'manual_qr' => ['manual', null],
            'manual_bank_details' => ['manual', ''],
            'manual_crypto' => ['manual', '[]'],
        ];

        foreach ($defaults as $key => [$group, $value]) {
            Setting::firstOrCreate(['key' => $key], ['group' => $group, 'value' => $value]);
        }
    }

    protected function seedCurrencies(): void
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 1, 'is_default' => true],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 0.92, 'is_default' => false],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'exchange_rate' => 0.79, 'is_default' => false],
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'exchange_rate' => 83.20, 'is_default' => false],
        ];

        foreach ($currencies as $c) {
            Currency::firstOrCreate(['code' => $c['code']], $c + ['is_active' => true]);
        }
    }
}
