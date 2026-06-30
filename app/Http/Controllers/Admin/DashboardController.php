<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with key metrics and recent activity.
     */
    public function index(): View
    {
        $completedOrders = Order::where('status', 'completed');

        $stats = [
            'revenue' => (float) (clone $completedOrders)->sum('total'),
            'orders' => (clone $completedOrders)->count(),
            'products' => Product::count(),
            'published' => Product::where('status', 'published')->count(),
            'customers' => User::where('role', 'user')->count(),
            'downloads' => (int) Product::sum('downloads'),
        ];

        // Revenue for the last 7 days for a simple chart.
        $salesByDay = collect(range(6, 0))->map(function (int $daysAgo): array {
            $date = Carbon::today()->subDays($daysAgo);

            $total = Order::where('status', 'completed')
                ->whereDate('paid_at', $date)
                ->sum('total');

            return [
                'label' => $date->format('M j'),
                'total' => (float) $total,
            ];
        });

        $recentOrders = Order::with('user')
            ->latest()
            ->take(8)
            ->get();

        $topProducts = Product::orderByDesc('downloads')
            ->take(5)
            ->get();

        $lowStockOrDraft = Product::where('status', 'draft')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'salesByDay',
            'recentOrders',
            'topProducts',
            'lowStockOrDraft'
        ));
    }
}
