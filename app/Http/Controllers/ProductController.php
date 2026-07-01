<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display the public product catalog with search, filter and sorting.
     */
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount('publishedProducts')
            ->orderBy('name')
            ->get();

        $products = $this->filteredProducts($request);

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => null,
            'filters' => $request->only(['q', 'category', 'sort', 'min', 'max']),
        ]);
    }

    /**
     * Display all products within a specific category.
     */
    public function category(Request $request, Category $category): View
    {
        abort_unless($category->is_active, 404);

        $categories = Category::query()
            ->where('is_active', true)
            ->withCount('publishedProducts')
            ->orderBy('name')
            ->get();

        $request->merge(['category' => $category->slug]);
        $products = $this->filteredProducts($request);

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => $category,
            'filters' => $request->only(['q', 'category', 'sort', 'min', 'max']),
        ]);
    }

    /**
     * Display a single product detail page.
     */
    public function show(Request $request, Product $product): View
    {
        abort_unless($product->status === 'published', 404);

        // Increment the view counter without bumping the updated_at timestamp.
        $product->incrementQuietly('views');

        $product->load(['category', 'approvedReviews.user', 'changelogs' => fn ($q) => $q->orderByDesc('released_at')]);

        $related = Product::query()
            ->published()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->getKey())
            ->with('category')
            ->take(4)
            ->get();

        $hasPurchased = $request->user()?->hasPurchased($product->id) ?? false;

        $canReview = $hasPurchased
            && ! $product->reviews()->where('user_id', $request->user()->id)->exists();

        return view('products.show', compact('product', 'related', 'hasPurchased', 'canReview'));
    }

    /**
     * Build a filtered, sorted and paginated product query from the request.
     *
     * @return LengthAwarePaginator<Product>
     */
    protected function filteredProducts(Request $request): LengthAwarePaginator
    {
        $query = Product::query()
            ->published()
            ->with('category')
            ->search($request->string('q')->toString() ?: null);

        if ($slug = $request->string('category')->toString()) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
        }

        if ($request->filled('min')) {
            $query->where('price', '>=', (float) $request->input('min'));
        }

        if ($request->filled('max')) {
            $query->where('price', '<=', (float) $request->input('max'));
        }

        match ($request->string('sort')->toString()) {
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'popular' => $query->orderByDesc('downloads'),
            'rating' => $query->orderByDesc('rating'),
            default => $query->latest(),
        };

        return $query->paginate(config('marketplace.per_page', 12))->withQueryString();
    }
}
