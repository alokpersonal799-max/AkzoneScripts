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
        ];

        return view('home', compact('featured', 'latest', 'topRated', 'bestSelling', 'freeItems', 'categories', 'testimonials', 'stats'));
    }
}
