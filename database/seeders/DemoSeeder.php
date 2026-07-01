<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductChangelog;
use App\Models\TelegramBot;
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
        $this->seedChangelogs();
        $this->seedPages();
        $this->seedContactMessages();
        $this->seedTelegramBot();
        $this->seedStorageSettings();
        $this->seedAutoTelegramPromo();
        $this->seedAdvertisements();
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

        // Promotional popup settings for demo/testing.
        $this->seedPopup();
    }

    /**
     * Seed promotional popup settings so the feature works out of the box.
     */
    protected function seedPopup(): void
    {
        \App\Models\Setting::put('popup_enabled', '1', 'promotion');
        \App\Models\Setting::put('popup_mode', 'message', 'promotion');
        \App\Models\Setting::put('popup_heading', 'Welcome!', 'promotion');
        \App\Models\Setting::put('popup_message', 'Check out our latest premium scripts and design assets. New products added weekly!', 'promotion');
        \App\Models\Setting::put('popup_link', '/products', 'promotion');
        \App\Models\Setting::put('popup_link_text', 'Browse Products', 'promotion');
        \App\Models\Setting::put('popup_auto_close_seconds', '10', 'promotion');
        \App\Models\Setting::put('popup_frequency', 'once', 'promotion');
        \App\Models\Setting::put('popup_product', '', 'promotion');
        \App\Models\Setting::put('popup_timer_until', '', 'promotion');
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

    /**
     * Seed version history / changelogs for select products.
     */
    protected function seedChangelogs(): void
    {
        $products = Product::whereIn('slug', [
            'invoiceflow-billing-invoicing-system',
            'laracommerce-starter-kit',
            'vuedash-analytics-dashboard',
            'nebula-saas-landing-template',
        ])->get();

        $changelogData = [
            'invoiceflow-billing-invoicing-system' => [
                ['version' => '1.0.0', 'notes' => 'Initial release with core invoicing, PDF export, and client management.', 'days_ago' => 90],
                ['version' => '1.1.0', 'notes' => 'Added recurring billing, email reminders, and multi-currency support.', 'days_ago' => 60],
                ['version' => '1.2.0', 'notes' => 'New dashboard analytics, tax calculation engine, and Stripe integration.', 'days_ago' => 30],
                ['version' => '1.3.0', 'notes' => 'Dark mode, bulk invoice generation, and performance improvements.', 'days_ago' => 7],
            ],
            'laracommerce-starter-kit' => [
                ['version' => '1.0.0', 'notes' => 'Initial release with product catalog, cart, and Stripe checkout.', 'days_ago' => 75],
                ['version' => '1.1.0', 'notes' => 'Added inventory management, wishlists, and order tracking API.', 'days_ago' => 45],
                ['version' => '1.2.0', 'notes' => 'Multi-vendor support, coupon system, and webhook integrations.', 'days_ago' => 15],
            ],
            'vuedash-analytics-dashboard' => [
                ['version' => '1.0.0', 'notes' => 'First release with real-time charts, data tables, and dark mode.', 'days_ago' => 60],
                ['version' => '1.1.0', 'notes' => 'Added export to PDF/CSV, custom date ranges, and widget builder.', 'days_ago' => 25],
                ['version' => '1.2.0', 'notes' => 'New map visualization, team collaboration features, and API connectors.', 'days_ago' => 5],
            ],
            'nebula-saas-landing-template' => [
                ['version' => '1.0.0', 'notes' => 'Initial release with 5 sections, animations, and responsive design.', 'days_ago' => 50],
                ['version' => '1.1.0', 'notes' => 'Added pricing table variants, testimonial carousel, and FAQ accordion.', 'days_ago' => 20],
            ],
        ];

        foreach ($products as $product) {
            $entries = $changelogData[$product->slug] ?? [];
            foreach ($entries as $entry) {
                ProductChangelog::updateOrCreate(
                    ['product_id' => $product->id, 'version' => $entry['version']],
                    [
                        'notes' => $entry['notes'],
                        'released_at' => now()->subDays($entry['days_ago']),
                    ]
                );
            }
        }
    }

    /**
     * Seed custom pages with HTML content and templates.
     */
    protected function seedPages(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content_type' => 'html',
                'content' => '<div class="prose max-w-none"><h2>About AkzoneScripts</h2><p>We are a passionate team of developers creating premium digital products for the modern web. Our scripts, templates, and tools are built with care, tested thoroughly, and designed to save you time.</p><h3>Our Mission</h3><p>To empower developers and businesses with high-quality, ready-to-use digital solutions that accelerate their projects from idea to launch.</p><h3>What We Offer</h3><ul><li>Production-ready PHP scripts and Laravel packages</li><li>Beautiful UI kits and landing page templates</li><li>Mobile app source code (Flutter &amp; React Native)</li><li>Premium design assets and icon packs</li></ul><p>Every product comes with detailed documentation, lifetime updates, and dedicated support.</p></div>',
                'is_published' => true,
                'show_in_footer' => true,
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'content_type' => 'html',
                'content' => '<div class="prose max-w-none"><h2>Terms of Service</h2><p><strong>Last updated:</strong> January 1, 2025</p><h3>1. Acceptance of Terms</h3><p>By accessing and using this marketplace, you agree to be bound by these Terms of Service. If you do not agree, please do not use our services.</p><h3>2. License Grant</h3><p>Upon purchasing a product, you receive a non-exclusive, non-transferable license to use the product for personal or commercial projects as described in the product listing.</p><h3>3. Restrictions</h3><ul><li>You may not redistribute, resell, or share purchased products</li><li>You may not use products in competing marketplace platforms</li><li>You may not remove copyright notices or credits</li></ul><h3>4. Refund Policy</h3><p>Due to the digital nature of our products, all sales are final. However, if a product has a critical defect, contact our support team within 7 days of purchase.</p><h3>5. Support</h3><p>Each product includes support as described in its listing. Support is provided via our ticket system and typically covers installation help and bug fixes.</p></div>',
                'is_published' => true,
                'show_in_footer' => true,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content_type' => 'html',
                'content' => '<div class="prose max-w-none"><h2>Privacy Policy</h2><p><strong>Last updated:</strong> January 1, 2025</p><h3>Information We Collect</h3><p>We collect information you provide directly: name, email, and payment details when you create an account or make a purchase.</p><h3>How We Use Your Information</h3><ul><li>Process transactions and deliver purchased products</li><li>Send order confirmations and product updates</li><li>Improve our services and user experience</li><li>Respond to support requests</li></ul><h3>Data Security</h3><p>We implement industry-standard security measures to protect your personal information. All payment processing is handled by trusted third-party providers.</p><h3>Contact Us</h3><p>If you have questions about this policy, contact us through our support system.</p></div>',
                'is_published' => true,
                'show_in_footer' => true,
            ],
            [
                'title' => 'FAQ',
                'slug' => 'faq',
                'content_type' => 'html',
                'content' => '<div class="prose max-w-none"><h2>Frequently Asked Questions</h2><h3>How do I download my purchase?</h3><p>After completing payment, go to your Dashboard and click "My Purchases". You will find download links for all your purchased products there.</p><h3>Do you offer refunds?</h3><p>Due to the digital nature of products, we generally do not offer refunds. If a product has a critical issue, contact support within 7 days.</p><h3>How long do I get updates?</h3><p>All products come with lifetime free updates. When we release a new version, it will be available in your purchases area.</p><h3>Can I use products for client projects?</h3><p>Yes! Our standard license allows use in personal and client projects. You cannot resell or redistribute the source code itself.</p><h3>How do I get support?</h3><p>Contact us via WhatsApp or Telegram using the links on any product page, or submit a support ticket from your dashboard.</p></div>',
                'is_published' => true,
                'show_in_footer' => true,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }

    /**
     * Seed sample contact messages for admin demonstration.
     */
    protected function seedContactMessages(): void
    {
        $messages = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'subject' => 'Bulk purchase discount inquiry',
                'message' => 'Hi, I am interested in purchasing multiple scripts for my development agency. Do you offer any bulk discount or agency license? We would need about 5-6 products. Please let me know your best pricing.',
                'ip' => '192.168.1.10',
                'read_at' => now()->subHours(2),
                'created_at' => now()->subDays(1),
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.j@example.com',
                'subject' => 'Custom development request',
                'message' => 'Hello! I love your InvoiceFlow script. Would it be possible to get a custom modification done? I need multi-tenant support added. Happy to pay for custom development work. Thanks!',
                'ip' => '10.0.0.55',
                'read_at' => null,
                'created_at' => now()->subHours(6),
            ],
            [
                'name' => 'Mike Chen',
                'email' => 'mike.chen@example.com',
                'subject' => 'Installation help needed',
                'message' => 'I purchased the LaraCommerce Starter Kit yesterday but I am having trouble with the installation on my shared hosting. The artisan commands are not working. Can you help me set it up? I am using cPanel with PHP 8.2.',
                'ip' => '172.16.0.100',
                'read_at' => null,
                'created_at' => now()->subHours(3),
            ],
            [
                'name' => 'Emma Wilson',
                'email' => 'emma.w@example.com',
                'subject' => 'Partnership proposal',
                'message' => 'Hi AkzoneScripts team! I run a web design agency and we frequently recommend premium scripts to our clients. Would you be interested in an affiliate or partnership arrangement? We can drive significant traffic your way.',
                'ip' => '192.168.50.25',
                'read_at' => now()->subDay(),
                'created_at' => now()->subDays(3),
            ],
            [
                'name' => 'David Park',
                'email' => 'david.park@example.com',
                'subject' => 'Feature request for VueDash',
                'message' => 'Great dashboard template! Would it be possible to add real-time WebSocket support in the next update? Also, an integration with Google Analytics API would be amazing. Keep up the great work!',
                'ip' => '10.10.10.1',
                'read_at' => null,
                'created_at' => now()->subHours(12),
            ],
        ];

        foreach ($messages as $msg) {
            ContactMessage::updateOrCreate(
                ['email' => $msg['email'], 'subject' => $msg['subject']],
                $msg
            );
        }
    }

    /**
     * Seed a placeholder Telegram bot entry for testing notifications.
     */
    protected function seedTelegramBot(): void
    {
        TelegramBot::updateOrCreate(
            ['name' => 'AkzoneScripts Notifications'],
            [
                'token' => '1234567890:ABCdefGHIjklMNOpqrsTUVwxyz_DEMO_TOKEN',
                'chat_id' => '-1001234567890',
                'events' => ['registration', 'purchase', 'review', 'free_download'],
                'is_active' => false,
            ]
        );
    }

    /**
     * Seed storage provider settings for demonstration.
     */
    protected function seedStorageSettings(): void
    {
        \App\Models\Setting::put('storage_provider', 'local', 'storage');
        \App\Models\Setting::put('storage_s3_key', '', 'storage');
        \App\Models\Setting::put('storage_s3_secret', '', 'storage');
        \App\Models\Setting::put('storage_s3_region', 'us-east-1', 'storage');
        \App\Models\Setting::put('storage_s3_bucket', '', 'storage');
    }

    /**
     * Seed auto Telegram promotion settings for demonstration.
     */
    protected function seedAutoTelegramPromo(): void
    {
        \App\Models\Setting::put('auto_tg_promo_enabled', '0', 'promotion');
        \App\Models\Setting::put('auto_tg_promo_interval', '24', 'promotion');
        \App\Models\Setting::put('auto_tg_promo_template', "New product available!\n\n{title}\n{price}\n\n{link}", 'promotion');
        \App\Models\Setting::put('auto_tg_promo_last_sent', '', 'promotion');
    }

    /**
     * Seed advertisement banner settings + demo banners so the feature is
     * ready to test right after installation.
     */
    protected function seedAdvertisements(): void
    {
        \App\Models\Setting::put('ads_enabled', '1', 'ads');
        \App\Models\Setting::put('ads_layout', '4', 'ads');
        \App\Models\Setting::put('ads_adsense_code', '', 'ads');
        \App\Models\Setting::put('ads_meta_code', '', 'ads');

        foreach (['marketplace', 'cart', 'checkout', 'dashboard', 'purchases', 'wishlist', 'support'] as $page) {
            \App\Models\Setting::put('ads_page_'.$page, '1', 'ads');
        }

        $banners = [
            ['title' => 'Your Ad Here', 'image' => 'https://placehold.co/600x300/2563eb/ffffff?text=Your+Ad+Here'],
            ['title' => 'Special Offer', 'image' => 'https://placehold.co/600x300/16a34a/ffffff?text=Special+Offer'],
            ['title' => 'Premium Scripts', 'image' => 'https://placehold.co/600x300/db2777/ffffff?text=Premium+Scripts'],
            ['title' => 'Limited Deal', 'image' => 'https://placehold.co/600x300/f59e0b/ffffff?text=Limited+Deal'],
            ['title' => 'Advertise Here', 'image' => 'https://placehold.co/600x300/7c3aed/ffffff?text=Advertise+Here'],
            ['title' => 'Boost Sales', 'image' => 'https://placehold.co/600x300/0ea5e9/ffffff?text=Boost+Sales'],
        ];

        foreach ($banners as $i => $banner) {
            Advertisement::updateOrCreate(
                ['title' => $banner['title']],
                [
                    'image_url' => $banner['image'],
                    'link_url' => 'https://example.com',
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ]
            );
        }
    }
}
