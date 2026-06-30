<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
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

        return view('checkout.index', [
            'items' => $items,
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    /**
     * Process the checkout and create a completed order.
     *
     * The bundled flow runs in "manual" payment mode so the full purchase
     * journey works out of the box. Wire in Stripe/PayPal in services.php to
     * accept live payments.
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
            return redirect()->route('products.index')
                ->with('info', 'Your cart is empty.');
        }

        // Skip products the customer already owns to avoid duplicate purchases.
        $items = $items->reject(fn ($product) => $user->hasPurchased($product->id))->values();

        if ($items->isEmpty()) {
            $this->cart->clear();

            return redirect()->route('dashboard.purchases')
                ->with('info', 'You already own everything that was in your cart.');
        }

        $order = DB::transaction(function () use ($user, $items, $validated): Order {
            $subtotal = (float) $items->sum(fn ($product) => $product->current_price);

            $order = Order::create([
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'tax' => 0,
                'total' => $subtotal,
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

                // Bump the product's download/sales counter.
                $product->incrementQuietly('downloads');
            }

            return $order;
        });

        $this->cart->clear();

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
}
