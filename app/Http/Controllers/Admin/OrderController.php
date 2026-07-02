<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * List all orders with optional status filtering.
     */
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with('user')
            ->withCount('items')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->toString();
                $query->where('order_number', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($q) => $q->where('email', 'like', "%{$term}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'filters' => $request->only(['q', 'status']),
        ]);
    }

    /**
     * Show a single order.
     */
    public function show(Order $order): View
    {
        $order->load(['user', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the status of an order.
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,completed,failed,refunded'],
        ]);

        $wasCompleted = $order->status === 'completed';
        $order->status = $validated['status'];

        // Stamp the paid_at timestamp the first time an order is completed.
        if ($validated['status'] === 'completed' && ! $order->paid_at) {
            $order->paid_at = now();
        }

        $order->save();

        // On first completion (e.g. verifying a manual payment): count the sale
        // for each product and email the buyer their receipt.
        if ($validated['status'] === 'completed' && ! $wasCompleted) {
            $order->loadMissing('items.product');

            foreach ($order->items as $item) {
                $item->product?->incrementQuietly('sales');
                $item->product?->decrementStock();
            }

            // Announce verified purchases to connected Telegram bots.
            $buyer = $order->user ?: new \App\Models\User(['name' => $order->billing_name, 'email' => $order->billing_email]);
            foreach ($order->items as $item) {
                if ($item->product) {
                    app(\App\Services\TelegramService::class)->notify('purchase', \App\Support\TelegramMessages::purchase($buyer, $item->product));
                }
            }

            try {
                \Illuminate\Support\Facades\Mail::to($order->billing_email)
                    ->send(new \App\Mail\OrderReceiptMail($order));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'Order status updated to '.$validated['status'].'.');
    }
}
