<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Show the user's wishlist.
     */
    public function index(Request $request): View
    {
        $products = $request->user()
            ->wishlistedProducts()
            ->with('category')
            ->latest('wishlists.created_at')
            ->paginate(12);

        return view('dashboard.wishlist', compact('products'));
    }

    /**
     * Toggle a product in the user's wishlist.
     */
    public function toggle(Request $request, Product $product): RedirectResponse
    {
        $user = $request->user();

        $result = $user->wishlistedProducts()->toggle($product->id);

        $message = ! empty($result['attached'])
            ? '"'.$product->title.'" was added to your wishlist.'
            : '"'.$product->title.'" was removed from your wishlist.';

        return back()->with('success', $message);
    }
}
