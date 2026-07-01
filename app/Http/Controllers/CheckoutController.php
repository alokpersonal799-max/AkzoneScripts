<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public const COUPON_SESSION = 'coupon_code';

    public function __construct(protected CartService $cart) {}

    /**
     * Payment methods enabled in System settings.
     *
     * @return array<string, string>
     */
    protected function enabledMethods(): array
    {
        $methods = [];
        if (setting('pay_manual_enabled', '1') === '1') {
            $methods['manual'] = 'Manual / Offline payment';
        }
        if (setting('pay_stripe_enabled') === '1') {
            $methods['stripe'] = 'Credit card (Stripe)';
        }
        if (setting('pay_paypal_enabled') === '1') {
            $methods['paypal'] = 'PayPal';
        }
        if (setting('pay_razorpay_enabled') === '1') {
            $methods['razorpay'] = 'Razorpay';
        }

        return $methods;
    }

    /**
     * Manual payment details configured by the admin.
     *
     * @return array<string, mixed>
     */
    protected function manualDetails(): array
    {
        return [
            'instructions' => setting('manual_instructions'),
            'upi' => setting('manual_upi_id'),
            'upi_enabled' => setting('manual_upi_enabled', '1') === '1',
            'qr' => setting('manual_qr') ? \Illuminate\Support\Facades\Storage::disk('public')->url(setting('manual_qr')) : null,
            'bank' => setting('manual_bank_details'),
            'bank_enabled' => setting('manual_bank_enabled', '1') === '1',
            'crypto' => json_decode(setting('manual_crypto', '[]'), true) ?: [],
            'crypto_enabled' => setting('manual_crypto_enabled', '1') === '1',
            'crypto_qr' => setting('manual_crypto_qr') ? \Illuminate\Support\Facades\Storage::disk('public')->url(setting('manual_crypto_qr')) : null,
        ];
    }

    /**
     * Show the checkout summary page.
     */
    public function index(): View|RedirectResponse
    {
        $allItems = $this->cart->items();

        if ($allItems->isEmpty()) {
            return redirect()->route('products.index')
                ->with('info', 'Your cart is empty. Browse the catalog to find something great.');
        }

        $items = $allItems->filter(fn ($p) => $p->is_purchasable)->values();
        $contactOnly = $allItems->reject(fn ($p) => $p->is_purchasable)->values();

        $subtotal = (float) $items->sum(fn ($p) => $p->current_price);
        $coupon = $this->activeCoupon($subtotal);
        $discount = $coupon ? $coupon->discountFor($subtotal) : 0;

        return view('checkout.index', [
            'items' => $items,
            'contactOnly' => $contactOnly,
            'subtotal' => $subtotal,
            'coupon' => $coupon,
            'discount' => $discount,
            'total' => max($subtotal - $discount, 0),
            'methods' => $this->enabledMethods(),
            'manual' => $this->manualDetails(),
        ]);
    }

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

    public function removeCoupon(): RedirectResponse
    {
        session()->forget(self::COUPON_SESSION);

        return back()->with('success', 'Coupon removed.');
    }

    /**
     * Process the checkout. Manual payments create a pending order awaiting
     * admin verification; enabled gateways complete immediately.
     */
    public function store(Request $request): RedirectResponse
    {
        $methods = $this->enabledMethods();

        $validated = $request->validate([
            'billing_name' => ['required', 'string', 'max:255'],
            'billing_email' => ['required', 'email', 'max:255'],
            'billing_phone' => ['nullable', 'string', 'max:30'],
            'billing_phone_country' => ['nullable', 'string', 'max:8'],
            'payment_method' => ['required', 'in:'.implode(',', array_keys($methods))],
            'transaction_id' => ['required_if:payment_method,manual', 'nullable', 'string', 'max:255'],
            'payment_proof' => ['required_if:payment_method,manual', 'nullable', 'image', 'max:5120'],
        ]);

        $user = $request->user();
        $items = $this->cart->items()
            ->reject(fn ($product) => $user->hasPurchased($product->id))
            ->filter(fn ($product) => $product->is_purchasable)
            ->values();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'The items in your cart are not available for direct checkout. Please contact us via WhatsApp or Telegram to purchase them.');
        }

        $subtotal = (float) $items->sum(fn ($product) => $product->current_price);
        $coupon = $this->activeCoupon($subtotal);
        $discount = $coupon ? $coupon->discountFor($subtotal) : 0;
        $isManual = $validated['payment_method'] === 'manual';

        // Store the payment proof screenshot for manual payments.
        $proofPath = null;
        if ($isManual && $request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        }

        $order = DB::transaction(function () use ($user, $items, $validated, $subtotal, $discount, $coupon, $isManual, $proofPath): Order {
            $order = Order::create([
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'tax' => 0,
                'discount' => $discount,
                'coupon_code' => $coupon?->code,
                'total' => max($subtotal - $discount, 0),
                'status' => $isManual ? 'pending' : 'completed',
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $isManual ? $validated['transaction_id'] : strtoupper('TXN-'.uniqid()),
                'payment_proof' => $proofPath,
                'billing_name' => $validated['billing_name'],
                'billing_email' => $validated['billing_email'],
                'billing_phone' => trim((($validated['billing_phone_country'] ?? '').' '.($validated['billing_phone'] ?? ''))) ?: null,
                'paid_at' => $isManual ? null : now(),
            ]);

            foreach ($items as $product) {
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_title' => $product->title,
                    'price' => $product->current_price,
                ]);

                // Only count a sale once the payment is confirmed.
                if (! $isManual) {
                    $product->incrementQuietly('sales');
                }
            }

            if ($coupon) {
                $coupon->increment('used_count');
            }

            return $order;
        });

        $this->cart->clear();
        session()->forget(self::COUPON_SESSION);

        if ($isManual) {
            \App\Models\AdminNotification::notifyAdmins('order', 'Payment to verify', $order->order_number.' · '.$order->billing_name, route('admin.orders.show', $order));

            return redirect()->route('orders.show', $order)
                ->with('info', 'Thanks! Your payment is awaiting verification. You will get access once an admin confirms it.');
        }

        \App\Models\AdminNotification::notifyAdmins('order', 'New order placed', $order->order_number.' · '.money($order->total), route('admin.orders.show', $order));

        // Announce each purchased product to connected Telegram bots.
        $order->loadMissing('items.product');
        foreach ($order->items as $item) {
            if ($item->product) {
                app(\App\Services\TelegramService::class)->notify('purchase', \App\Support\TelegramMessages::purchase($user, $item->product));
            }
        }

        try {
            Mail::to($order->billing_email)->send(new \App\Mail\OrderReceiptMail($order->load('items')));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Payment successful! Your digital products are ready to download.');
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load('items.product');

        return view('checkout.show', compact('order'));
    }

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
