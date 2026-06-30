<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the member dashboard overview.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $orders = $user->orders()->where('status', 'completed')->get();

        $stats = [
            'purchases' => $orders->sum(fn ($order) => $order->items->count() ?: $order->items()->count()),
            'orders' => $orders->count(),
            'spent' => (float) $orders->sum('total'),
            'wishlist' => $user->wishlists()->count(),
        ];

        $recentOrders = $user->orders()
            ->with('items')
            ->latest()
            ->take(5)
            ->get();

        $recentDownloads = $user->purchasedProducts()
            ->take(4)
            ->get();

        return view('dashboard.index', compact('stats', 'recentOrders', 'recentDownloads'));
    }

    /**
     * Show the member's purchased products and download links.
     */
    public function purchases(Request $request): View
    {
        $user = $request->user();

        $orderItems = \App\Models\OrderItem::query()
            ->whereHas('order', function ($query) use ($user): void {
                $query->where('user_id', $user->id)->where('status', 'completed');
            })
            ->with(['product.category', 'order'])
            ->latest()
            ->paginate(12);

        return view('dashboard.purchases', compact('orderItems'));
    }
}
