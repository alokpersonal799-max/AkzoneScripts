<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Lightweight demo data used by the web installer.
 *
 * Seeds categories and products only — no users, orders or reviews — so the
 * storefront has content to show right after installation. The admin account
 * is created separately by the installer's "Site & Admin" step.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CoreSeeder::class);
        $this->seedDemoUsers();
        $this->seedDemoSettings();
        $categories = $this->seedCategories();
        $this->seedProducts($categories);
        $this->seedPromotion();
    }

    /**
     * Seed a ready-to-view hero promotion (featured products mode) so the
     * homepage shows the promotion band immediately for testing.
     */
    protected function seedPromotion(): void
    {
        $ids = Product::query()->where('status', 'published')->orderByDesc('is_featured')->take(4)->pluck('id')->all();

        \App\Models\Setting::put('promo_mode', 'products', 'promotion');
        \App\Models\Setting::put('promo_heading', 'Featured picks', 'promotion');
        \App\Models\Setting::put('promo_products', json_encode($ids), 'promotion');

        // Pre-fill (but don't activate) a sample countdown + message so admins
        // can switch modes and immediately see something working.
        $countdownId = $ids[0] ?? null;
        if ($countdownId) {
            \App\Models\Setting::put('promo_countdown_product', (string) $countdownId, 'promotion');
        }
        \App\Models\Setting::put('promo_countdown_label', 'Flash deal — ends soon', 'promotion');
        \App\Models\Setting::put('promo_countdown_until', now()->addDays(3)->format('Y-m-d H:i:s'), 'promotion');

        // A second optional countdown offer.
        $countdownId2 = $ids[1] ?? null;
        if ($countdownId2) {
            \App\Models\Setting::put('promo_countdown_product_2', (string) $countdownId2, 'promotion');
            \App\Models\Setting::put('promo_countdown_label_2', 'Weekend special', 'promotion');
            \App\Models\Setting::put('promo_countdown_until_2', now()->addDays(2)->format('Y-m-d H:i:s'), 'promotion');
        }

        \App\Models\Setting::put('promo_message', '🎉 Launch week — use code AKZONE10 for 10% off your first order!', 'promotion');
    }

    /**
     * Demo contact + manual payment details so the buttons/methods show up.
     * Replace these with your real details in System settings.
     */
    protected function seedDemoSettings(): void
    {
        \App\Models\Setting::put('contact_whatsapp', '14155550123', 'general');
        \App\Models\Setting::put('contact_telegram', 'akzonescripts', 'general');
        \App\Models\Setting::put('manual_upi_id', 'akzonescripts@upi', 'manual');
        \App\Models\Setting::put('manual_bank_details', "Account: AkzoneScripts\nA/C No: 0000 1111 2222\nIFSC: AKZN0001234\nBank: Demo Bank", 'manual');
        \App\Models\Setting::put('manual_crypto', json_encode([
            ['label' => 'USDT (TRC20)', 'address' => 'TXyZ0000DemoWalletAddress1111'],
            ['label' => 'Bitcoin', 'address' => 'bc1qdemo0000walletaddress2222'],
        ]), 'manual');
    }

    /**
     * Create ready-to-use demo accounts for testing both panels.
     * Change or remove these before going live in production.
     */
    protected function seedDemoUsers(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@akzone.com'],
            [
                'name' => 'Demo Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'last_login_at' => now()->subMinutes(8),
                'last_login_ip' => '127.0.0.1',
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'user@akzone.com'],
            [
                'name' => 'Demo Customer',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
                'last_login_at' => now()->subHours(3),
                'last_login_ip' => '127.0.0.1',
            ]
        );
    }

    /**
     * @return \Illuminate\Support\Collection<int, Category>
     */
    protected function seedCategories()
    {
        $data = [
            ['name' => 'PHP Scripts', 'icon' => '🐘', 'description' => 'Production-ready PHP scripts and full applications.'],
            ['name' => 'Laravel Packages', 'icon' => '🅛', 'description' => 'Reusable Laravel packages and starter kits.'],
            ['name' => 'JavaScript', 'icon' => '⚡', 'description' => 'Vue, React and vanilla JS tools and widgets.'],
            ['name' => 'UI Kits & Templates', 'icon' => '🎨', 'description' => 'Beautifully crafted UI kits, themes and HTML templates.'],
            ['name' => 'Mobile Apps', 'icon' => '📱', 'description' => 'Flutter and React Native app source code.'],
            ['name' => 'Design Assets', 'icon' => '🖌️', 'description' => 'Icons, illustrations and Figma design files.'],
        ];

        return collect($data)->map(fn (array $c) => Category::updateOrCreate(
            ['slug' => Str::slug($c['name'])],
            $c + ['is_active' => true]
        ));
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Category>  $categories
     */
    protected function seedProducts($categories): void
    {
        $bySlug = $categories->keyBy('slug');

        $products = [
            ['cat' => 'php-scripts', 'title' => 'InvoiceFlow — Billing & Invoicing System', 'tagline' => 'Complete invoicing SaaS with PDF export and recurring billing.', 'price' => 49, 'sale' => 39, 'featured' => true, 'tags' => ['php', 'invoicing', 'saas', 'mysql']],
            ['cat' => 'php-scripts', 'title' => 'SupportDesk — Helpdesk & Ticketing', 'tagline' => 'Multi-agent support ticket system with email piping.', 'price' => 39, 'featured' => false, 'tags' => ['php', 'support', 'tickets']],
            ['cat' => 'laravel-packages', 'title' => 'LaraCommerce Starter Kit', 'tagline' => 'Headless e-commerce starter built on Laravel 12.', 'price' => 79, 'sale' => 59, 'featured' => true, 'tags' => ['laravel', 'ecommerce', 'api']],
            ['cat' => 'laravel-packages', 'title' => 'AuthGuard — Roles & Permissions', 'tagline' => 'Drop-in RBAC package with a polished admin UI.', 'price' => 0, 'featured' => false, 'tags' => ['laravel', 'auth', 'permissions']],
            ['cat' => 'javascript', 'title' => 'VueDash — Analytics Dashboard', 'tagline' => 'Real-time analytics dashboard built with Vue 3 + Vite.', 'price' => 35, 'featured' => true, 'tags' => ['vue', 'dashboard', 'charts'], 'file_type' => 'external', 'external_url' => 'https://example.com/downloads/vuedash-v1.zip', 'download_limit' => 3, 'link_expiry' => 60, 'download_message' => 'Your link is valid for 60 minutes and can be used up to 3 times. Re-open My Purchases for a fresh link.'],
            ['cat' => 'javascript', 'title' => 'DropZone Pro — File Uploader', 'tagline' => 'Chunked, resumable file uploads for any backend.', 'price' => 19, 'featured' => false, 'tags' => ['javascript', 'uploads']],
            ['cat' => 'ui-kits-templates', 'title' => 'Nebula — SaaS Landing Template', 'tagline' => 'Modern, animated SaaS landing page in Tailwind CSS.', 'price' => 24, 'sale' => 18, 'featured' => true, 'tags' => ['tailwind', 'landing', 'html'], 'file_type' => 'external', 'external_url' => 'https://example.com/downloads/nebula-template.zip', 'download_limit' => 0, 'download_message' => 'Unlimited lifetime downloads. Thank you for your purchase!'],
            ['cat' => 'ui-kits-templates', 'title' => 'Orbit Admin — Dashboard UI Kit', 'tagline' => '120+ components and 30 pages for admin panels.', 'price' => 45, 'featured' => false, 'tags' => ['ui-kit', 'admin', 'tailwind']],
            ['cat' => 'mobile-apps', 'title' => 'FoodieGo — Food Delivery App', 'tagline' => 'Full Flutter food delivery app with backend API.', 'price' => 89, 'featured' => true, 'tags' => ['flutter', 'mobile', 'delivery'], 'sellable' => false],
            ['cat' => 'mobile-apps', 'title' => 'FitTrack — Workout Tracker', 'tagline' => 'React Native fitness tracking app source code.', 'price' => 55, 'featured' => false, 'tags' => ['react-native', 'fitness'], 'sellable' => false],
            ['cat' => 'design-assets', 'title' => 'Lumina Icon Pack — 2,400 Icons', 'tagline' => 'Pixel-perfect line and solid icons in SVG & Figma.', 'price' => 15, 'featured' => false, 'tags' => ['icons', 'svg', 'figma']],
            ['cat' => 'design-assets', 'title' => 'Gradient Mesh Backgrounds Vol. 1', 'tagline' => '60 high-res abstract gradient backgrounds.', 'price' => 9, 'sale' => 5, 'featured' => false, 'tags' => ['backgrounds', 'design']],
        ];

        foreach ($products as $p) {
            $category = $bySlug->get($p['cat']);

            if (! $category) {
                continue;
            }

            Product::updateOrCreate(
                ['slug' => Str::slug($p['title'])],
                [
                    'category_id' => $category->id,
                    'title' => $p['title'],
                    'tagline' => $p['tagline'],
                    'description' => $this->description($p['title'], $p['tagline']),
                    'price' => $p['price'],
                    'sale_price' => $p['sale'] ?? null,
                    'version' => '1.'.rand(0, 9).'.'.rand(0, 9),
                    'demo_url' => 'https://example.com/demo',
                    'tags' => $p['tags'],
                    'downloads' => rand(12, 1850),
                    'sales' => rand(5, 600),
                    'views' => rand(200, 9000),
                    'status' => 'published',
                    'is_featured' => $p['featured'],
                    'is_purchasable' => $p['sellable'] ?? true,
                    'use_global_contact' => true,
                    'file_type' => $p['file_type'] ?? 'upload',
                    'external_url' => $p['external_url'] ?? null,
                    'download_limit' => $p['download_limit'] ?? null,
                    'link_expiry_minutes' => $p['link_expiry'] ?? null,
                    'download_message' => $p['download_message'] ?? null,
                ]
            );
        }
    }

    protected function description(string $title, string $tagline): string
    {
        return "{$tagline}\n\n".
            "{$title} is a premium, production-ready product crafted for developers and teams who value clean code and great design. ".
            "Everything is fully documented, easy to customise, and ships with lifetime updates.\n\n".
            "What's included:\n".
            "- Complete, well-commented source code\n".
            "- Step-by-step installation guide\n".
            "- 6 months of premium support\n".
            "- Free lifetime updates\n\n".
            "Requirements: a modern development environment. See the included documentation for full setup instructions.";
    }
}
