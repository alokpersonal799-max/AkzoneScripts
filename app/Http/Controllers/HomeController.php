<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the marketplace landing page.
     */
    public function index(): View
    {
        $featured = Product::query()
            ->published()
            ->featured()
            ->with('category')
            ->latest()
            ->take(6)
            ->get();

        $latest = Product::query()
            ->published()
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        $topRated = Product::query()
            ->published()
            ->with('category')
            ->orderByDesc('rating')
            ->orderByDesc('reviews_count')
            ->take(4)
            ->get();

        $bestSelling = Product::query()
            ->published()
            ->with('category')
            ->orderByDesc('sales')
            ->take(8)
            ->get();

        $freeItems = Product::query()
            ->published()
            ->with('category')
            ->where(function ($q) {
                $q->where('price', '<=', 0)
                    ->orWhere('sale_price', '<=', 0);
            })
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::query()
            ->where('is_active', true)
            ->withCount('publishedProducts')
            ->orderBy('name')
            ->get();

        $testimonials = \App\Models\Review::query()
            ->testimonials()
            ->with(['user', 'product'])
            ->latest()
            ->take(8)
            ->get();

        $stats = [
            'products' => Product::published()->count(),
            'sold' => (int) Product::published()->sum('sales'),
            'categories' => $categories->count(),
            'free' => Product::published()->where(function ($q) {
                $q->where('price', '<=', 0)->orWhere('sale_price', '<=', 0);
            })->count(),
            'downloads' => (int) Product::published()->where(function ($q) {
                $q->where('price', '<=', 0)->orWhere('sale_price', '<=', 0);
            })->sum('downloads'),
        ];

        $promotion = $this->resolvePromotion();

        return view('home', compact('featured', 'latest', 'topRated', 'bestSelling', 'freeItems', 'categories', 'testimonials', 'stats', 'promotion'));
    }

    /**
     * Build the active hero promotion (managed from the admin Promotions page).
     *
     * Modes: off | products | message | countdown.
     *
     * @return array<string, mixed>
     */
    protected function resolvePromotion(): array
    {
        $mode = setting('promo_mode', 'off');

        if ($mode === 'products') {
            $ids = json_decode((string) setting('promo_products', '[]'), true) ?: [];
            if (! empty($ids)) {
                $products = Product::published()->with('category')->whereIn('id', $ids)->take(4)->get()
                    ->sortBy(fn ($p) => array_search($p->id, $ids))->values();
                if ($products->isNotEmpty()) {
                    return [
                        'mode' => 'products',
                        'heading' => setting('promo_heading', 'Featured picks'),
                        'products' => $products,
                    ];
                }
            }
        }

        if ($mode === 'message') {
            $message = setting('promo_message');
            if ($message) {
                return [
                    'mode' => 'message',
                    'heading' => setting('promo_heading', ''),
                    'message' => $message,
                    'url' => setting('promo_message_url', ''),
                ];
            }
        }

        if ($mode === 'countdown') {
            $offers = [];
            $sets = [
                ['promo_countdown_product', 'promo_countdown_label', 'promo_countdown_until'],
                ['promo_countdown_product_2', 'promo_countdown_label_2', 'promo_countdown_until_2'],
            ];
            foreach ($sets as $keys) {
                $pid = setting($keys[0]);
                $until = setting($keys[2]);
                $product = $pid ? Product::published()->with('category')->find($pid) : null;
                if ($product && $until && \Illuminate\Support\Carbon::parse($until)->isFuture()) {
                    $offers[] = [
                        'label' => setting($keys[1], 'Limited time offer'),
                        'product' => $product,
                        'until' => \Illuminate\Support\Carbon::parse($until)->toIso8601String(),
                    ];
                }
            }
            if (! empty($offers)) {
                return ['mode' => 'countdown', 'offers' => $offers];
            }
        }

        return ['mode' => 'off'];
    }
}
