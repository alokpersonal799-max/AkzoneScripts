<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public const COUPON_SESSION = 'coupon_code';

    public function __construct(protected CartService $cart) {}

    /**
     * Show the checkout summary page.
     */
    public function index(): View|RedirectResponse
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            return redirect()->route('products.index')
                ->with('info', 'Your cart is empty. Browse the catalog to find something great.');
        }

        $subtotal = $this->cart->subtotal();
        $coupon = $this->activeCoupon($subtotal);
        $discount = $coupon ? $coupon->discountFor($subtotal) : 0;

        return view('checkout.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'coupon' => $coupon,
            'discount' => $discount,
            'total' => max($subtotal - $discount, 0),
        ]);
    }

    /**
     * Apply a coupon code to the current cart.
     */
    public function applyCoupon(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string', 'max:50']]);

        $coupon = Coupon::where('code', strtoupper($request->string('code')->toString()))->first();

        if (! $coupon || ! $coupon->isValid()) {
            return back()->with('error', 'That coupon code is invalid or expired.');
        }

        if ($this->cart->subtotal() < (float) $coupon->min_order) {
            return back()->with('error', 'Your order does not meet the minimum for this coupon.');
        }

        session([self::COUPON_SESSION => $coupon->code]);

        return back()->with('success', 'Coupon "'.$coupon->code.'" applied.');
    }

    /**
     * Remove an applied coupon.
     */
    public function removeCoupon(): RedirectResponse
    {
        session()->forget(self::COUPON_SESSION);

        return back()->with('success', 'Coupon removed.');
    }

    /**
     * Process the checkout and create a completed order.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'billing_name' => ['required', 'string', 'max:255'],
            'billing_email' => ['required', 'email', 'max:255'],
            'payment_method' => ['required', 'in:manual,stripe,paypal'],
        ]);

        $user = $request->user();
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            return redirect()->route('products.index')->with('info', 'Your cart is empty.');
        }

        // Skip products the customer already owns to avoid duplicate purchases.
        $items = $items->reject(fn ($product) => $user->hasPurchased($product->id))->values();

        if ($items->isEmpty()) {
            $this->cart->clear();

            return redirect()->route('dashboard.purchases')
                ->with('info', 'You already own everything that was in your cart.');
        }

        $subtotal = (float) $items->sum(fn ($product) => $product->current_price);
        $coupon = $this->activeCoupon($subtotal);
        $discount = $coupon ? $coupon->discountFor($subtotal) : 0;

        $order = DB::transaction(function () use ($user, $items, $validated, $subtotal, $discount, $coupon): Order {
            $order = Order::create([
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'tax' => 0,
                'discount' => $discount,
                'coupon_code' => $coupon?->code,
                'total' => max($subtotal - $discount, 0),
                'status' => 'completed',
                'payment_method' => $validated['payment_method'],
                'transaction_id' => strtoupper('TXN-'.uniqid()),
                'billing_name' => $validated['billing_name'],
                'billing_email' => $validated['billing_email'],
                'paid_at' => now(),
            ]);

            foreach ($items as $product) {
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_title' => $product->title,
                    'price' => $product->current_price,
                ]);

                $product->incrementQuietly('downloads');
            }

            if ($coupon) {
                $coupon->increment('used_count');
            }

            return $order;
        });

        $this->cart->clear();
        session()->forget(self::COUPON_SESSION);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Payment successful! Your digital products are ready to download.');
    }

    /**
     * Show an order confirmation / receipt.
     */
    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load('items.product');

        return view('checkout.show', compact('order'));
    }

    /**
     * Resolve the currently applied, still-valid coupon (if any).
     */
    protected function activeCoupon(float $subtotal): ?Coupon
    {
        $code = session(self::COUPON_SESSION);

        if (! $code) {
            return null;
        }

        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon || ! $coupon->isValid() || $subtotal < (float) $coupon->min_order) {
            session()->forget(self::COUPON_SESSION);

            return null;
        }

        return $coupon;
    }
}
