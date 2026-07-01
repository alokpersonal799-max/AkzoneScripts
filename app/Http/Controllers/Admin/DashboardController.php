<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with key metrics and analytics.
     */
    public function index(): View
    {
        $completedOrders = Order::where('status', 'completed');

        $totalViews = (int) Product::sum('views');
        $completedCount = (clone $completedOrders)->count();

        $freeProductIds = Product::query()->where(function ($q) {
            $q->where('price', '<=', 0)->orWhere('sale_price', '<=', 0);
        })->pluck('id');

        $stats = [
            'revenue' => (float) (clone $completedOrders)->sum('total'),
            'revenue_today' => (float) Order::where('status', 'completed')->whereDate('paid_at', Carbon::today())->sum('total'),
            'orders_today' => Order::whereDate('created_at', Carbon::today())->count(),
            'orders' => $completedCount,
            'orders_pending' => Order::where('status', 'pending')->count(),
            'products' => Product::count(),
            'published' => Product::where('status', 'published')->count(),
            'customers' => User::where('role', 'user')->count(),
            'customers_today' => User::where('role', 'user')->whereDate('created_at', Carbon::today())->count(),
            'sold' => (int) Product::sum('sales'),
            'downloads' => (int) Product::whereIn('id', $freeProductIds)->sum('downloads'),
            'free_products' => $freeProductIds->count(),
            'views' => $totalViews,
            'services' => \Illuminate\Support\Facades\Schema::hasTable('services') ? \App\Models\Service::where('is_active', true)->count() : 0,
            'conversion' => $totalViews > 0 ? round(($completedCount / $totalViews) * 100, 2) : 0,
        ];

        $recentUsers = User::where('role', 'user')->latest()->take(6)->get();

        // Recently active users (most recent sign-ins) for the activity panel.
        $recentLogins = \Illuminate\Support\Facades\Schema::hasColumn('users', 'last_login_at')
            ? User::whereNotNull('last_login_at')->orderByDesc('last_login_at')->take(6)->get()
            : collect();

        // Revenue for the last 14 days.
        $salesByDay = collect(range(13, 0))->map(function (int $daysAgo): array {
            $date = Carbon::today()->subDays($daysAgo);

            return [
                'label' => $date->format('M j'),
                'total' => (float) Order::where('status', 'completed')->whereDate('paid_at', $date)->sum('total'),
            ];
        });

        // Top categories by revenue (joins completed order items -> products).
        $topCategories = Category::query()
            ->select('categories.name', DB::raw('COALESCE(SUM(order_items.price), 0) as revenue'), DB::raw('COUNT(order_items.id) as sales'))
            ->leftJoin('products', 'products.category_id', '=', 'categories.id')
            ->leftJoin('order_items', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('orders', function ($join) {
                $join->on('orders.id', '=', 'order_items.order_id')->where('orders.status', '=', 'completed');
            })
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        $recentOrders = Order::with('user')->latest()->take(8)->get();
        $topProducts = Product::orderByDesc('downloads')->take(5)->get();
        $lowStockOrDraft = Product::where('status', 'draft')->latest()->take(5)->get();

        // Announcement snapshot.
        $announcementStats = null;
        if (\Illuminate\Support\Facades\Schema::hasTable('announcements')) {
            $announcementStats = [
                'total' => \App\Models\Announcement::count(),
                'sent' => \App\Models\Announcement::where('status', 'sent')->count(),
                'scheduled' => \App\Models\Announcement::where('status', 'scheduled')->count(),
                'replies' => \Illuminate\Support\Facades\Schema::hasTable('announcement_replies') ? \App\Models\AnnouncementReply::where('is_admin', false)->count() : 0,
                'recent' => \App\Models\Announcement::latest()->take(3)->get(),
            ];
        }

        return view('admin.dashboard', compact(
            'stats',
            'salesByDay',
            'topCategories',
            'recentOrders',
            'recentUsers',
            'recentLogins',
            'topProducts',
            'lowStockOrDraft',
            'announcementStats'
        ));
    }
}
