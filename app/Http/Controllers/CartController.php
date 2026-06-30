<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    /**
     * Show the current cart contents.
     */
    public function index(): View
    {
        return view('cart.index', [
            'items' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    /**
     * Add a product to the cart.
     */
    public function add(Product $product): RedirectResponse
    {
        abort_unless($product->status === 'published', 404);

        if ($this->cart->has($product->id)) {
            return back()->with('info', '"'.$product->title.'" is already in your cart.');
        }

        $this->cart->add($product);

        return back()->with('success', '"'.$product->title.'" was added to your cart.');
    }

    /**
     * Add a product and go straight to checkout (Buy Now).
     */
    public function buyNow(Product $product): RedirectResponse
    {
        abort_unless($product->status === 'published', 404);

        if (! $product->is_purchasable) {
            return back()->with('error', '"'.$product->title.'" is not available for direct purchase. Please contact us via WhatsApp or Telegram to buy it.');
        }

        $this->cart->add($product);

        return redirect()->route('checkout.index');
    }

    /**
     * Remove a product from the cart.
     */
    public function remove(Product $product): RedirectResponse
    {
        $this->cart->remove($product->id);

        return back()->with('success', 'Item removed from your cart.');
    }

    /**
     * Empty the cart.
     */
    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return back()->with('success', 'Your cart has been cleared.');
    }
}
