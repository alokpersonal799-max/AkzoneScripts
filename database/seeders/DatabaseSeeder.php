<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with demo marketplace data.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@akzonescripts.test'],
            [
                'name' => 'Akzone Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'bio' => 'Owner and curator of AkzoneScripts.',
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'customer@akzonescripts.test'],
            [
                'name' => 'Jane Developer',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        // A handful of extra demo customers.
        $customers = User::factory()->count(8)->create()->push($customer);

        $categories = $this->seedCategories();
        $products = $this->seedProducts($categories);

        $this->seedReviews($products, $customers);
        $this->seedOrders($products, $customer);

        $this->command->info('Seeded '.$categories->count().' categories and '.$products->count().' products.');
        $this->command->info('Admin login:    admin@akzonescripts.test / password');
        $this->command->info('Customer login: customer@akzonescripts.test / password');
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
            ['slug' => \Illuminate\Support\Str::slug($c['name'])],
            $c + ['is_active' => true]
        ));
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Category>  $categories
     * @return \Illuminate\Support\Collection<int, Product>
     */
    protected function seedProducts($categories)
    {
        $bySlug = $categories->keyBy('slug');

        $products = [
            ['cat' => 'php-scripts', 'title' => 'InvoiceFlow — Billing & Invoicing System', 'tagline' => 'Complete invoicing SaaS with PDF export and recurring billing.', 'price' => 49, 'sale' => 39, 'featured' => true, 'tags' => ['php', 'invoicing', 'saas', 'mysql']],
            ['cat' => 'php-scripts', 'title' => 'SupportDesk — Helpdesk & Ticketing', 'tagline' => 'Multi-agent support ticket system with email piping.', 'price' => 39, 'featured' => false, 'tags' => ['php', 'support', 'tickets']],
            ['cat' => 'laravel-packages', 'title' => 'LaraCommerce Starter Kit', 'tagline' => 'Headless e-commerce starter built on Laravel 12.', 'price' => 79, 'sale' => 59, 'featured' => true, 'tags' => ['laravel', 'ecommerce', 'api']],
            ['cat' => 'laravel-packages', 'title' => 'AuthGuard — Roles & Permissions', 'tagline' => 'Drop-in RBAC package with a polished admin UI.', 'price' => 0, 'featured' => false, 'tags' => ['laravel', 'auth', 'permissions']],
            ['cat' => 'javascript', 'title' => 'VueDash — Analytics Dashboard', 'tagline' => 'Real-time analytics dashboard built with Vue 3 + Vite.', 'price' => 35, 'featured' => true, 'tags' => ['vue', 'dashboard', 'charts']],
            ['cat' => 'javascript', 'title' => 'DropZone Pro — File Uploader', 'tagline' => 'Chunked, resumable file uploads for any backend.', 'price' => 19, 'featured' => false, 'tags' => ['javascript', 'uploads']],
            ['cat' => 'ui-kits-templates', 'title' => 'Nebula — SaaS Landing Template', 'tagline' => 'Modern, animated SaaS landing page in Tailwind CSS.', 'price' => 24, 'sale' => 18, 'featured' => true, 'tags' => ['tailwind', 'landing', 'html']],
            ['cat' => 'ui-kits-templates', 'title' => 'Orbit Admin — Dashboard UI Kit', 'tagline' => '120+ components and 30 pages for admin panels.', 'price' => 45, 'featured' => false, 'tags' => ['ui-kit', 'admin', 'tailwind']],
            ['cat' => 'mobile-apps', 'title' => 'FoodieGo — Food Delivery App', 'tagline' => 'Full Flutter food delivery app with backend API.', 'price' => 89, 'featured' => true, 'tags' => ['flutter', 'mobile', 'delivery']],
            ['cat' => 'mobile-apps', 'title' => 'FitTrack — Workout Tracker', 'tagline' => 'React Native fitness tracking app source code.', 'price' => 55, 'featured' => false, 'tags' => ['react-native', 'fitness']],
            ['cat' => 'design-assets', 'title' => 'Lumina Icon Pack — 2,400 Icons', 'tagline' => 'Pixel-perfect line and solid icons in SVG & Figma.', 'price' => 15, 'featured' => false, 'tags' => ['icons', 'svg', 'figma']],
            ['cat' => 'design-assets', 'title' => 'Gradient Mesh Backgrounds Vol. 1', 'tagline' => '60 high-res abstract gradient backgrounds.', 'price' => 9, 'sale' => 5, 'featured' => false, 'tags' => ['backgrounds', 'design']],
        ];

        return collect($products)->map(function (array $p) use ($bySlug) {
            $category = $bySlug->get($p['cat']);

            return Product::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($p['title'])],
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
                    'views' => rand(200, 9000),
                    'status' => 'published',
                    'is_featured' => $p['featured'],
                    // Note: file_path is intentionally left null in seed data.
                    // Upload a real package via the admin panel to enable downloads.
                ]
            );
        });
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
     * @param  \Illuminate\Support\Collection<int, Product>  $products
     * @param  \Illuminate\Support\Collection<int, User>  $customers
     */
    protected function seedReviews($products, $customers): void
    {
        foreach ($products as $product) {
            $reviewers = $customers->random(min(rand(0, 4), $customers->count()));

            foreach ($reviewers as $user) {
                Review::updateOrCreate(
                    ['product_id' => $product->id, 'user_id' => $user->id],
                    [
                        'rating' => rand(3, 5),
                        'comment' => collect([
                            'Excellent quality and very easy to integrate.',
                            'Saved me weeks of development time. Highly recommended.',
                            'Clean, well-documented code. Worth every penny.',
                            'Great product and the support is responsive.',
                            'Exactly what I needed for my project.',
                        ])->random(),
                        'is_approved' => true,
                    ]
                );
            }
        }
    }

    /**
     * Create a sample completed order for the demo customer.
     *
     * @param  \Illuminate\Support\Collection<int, Product>  $products
     */
    protected function seedOrders($products, User $customer): void
    {
        $purchased = $products->where('price', '>', 0)->random(min(2, $products->count()));
        $subtotal = (float) $purchased->sum(fn (Product $p) => $p->current_price);

        $order = Order::create([
            'user_id' => $customer->id,
            'subtotal' => $subtotal,
            'tax' => 0,
            'total' => $subtotal,
            'status' => 'completed',
            'payment_method' => 'manual',
            'transaction_id' => 'TXN-DEMO-SEED',
            'billing_name' => $customer->name,
            'billing_email' => $customer->email,
            'paid_at' => now()->subDays(2),
        ]);

        foreach ($purchased as $product) {
            $order->items()->create([
                'product_id' => $product->id,
                'product_title' => $product->title,
                'price' => $product->current_price,
            ]);
        }
    }
}
