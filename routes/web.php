<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CurrencyController as AdminCurrencyController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Installer (locked automatically after setup completes)
|--------------------------------------------------------------------------
*/

Route::controller(InstallController::class)->prefix('install')->name('install.')->group(function () {
    Route::get('/', 'requirements')->name('requirements');
    Route::get('/permissions', 'permissions')->name('permissions');
    Route::get('/database', 'database')->name('database');
    Route::post('/database', 'saveDatabase')->name('database.save');
    Route::get('/import', 'import')->name('import');
    Route::post('/import', 'runImport')->name('import.run');
    Route::get('/account', 'account')->name('account');
    Route::post('/account', 'saveAccount')->name('account.save');
    Route::get('/finish', 'finish')->name('finish');
});

/*
|--------------------------------------------------------------------------
| Public Storefront
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category}', [ProductController::class, 'category'])->name('categories.show');

// Switch the active display currency.
Route::get('/currency/{code}', [CurrencyController::class, 'switch'])->name('currency.switch');

// Shopping cart (session based, available to guests and members).
Route::controller(CartController::class)->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/add/{product}', 'add')->name('add');
    Route::post('/buy/{product}', 'buyNow')->name('buy');
    Route::delete('/remove/{product}', 'remove')->name('remove');
    Route::delete('/clear', 'clear')->name('clear');
});

/*
|--------------------------------------------------------------------------
| Guest Only (Authentication)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Members
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Member dashboard.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/purchases', [DashboardController::class, 'purchases'])->name('dashboard.purchases');

    // Profile management.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Checkout & orders.
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon.apply');
    Route::delete('/checkout/coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.coupon.remove');
    Route::get('/orders/{order}', [CheckoutController::class, 'show'])->name('orders.show');

    // Secure download of a purchased product.
    Route::get('/download/{orderItem}', [DownloadController::class, 'download'])->name('download');

    // Reviews & reports.
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/products/{product}/report', [ReportController::class, 'store'])->name('reports.store');

    // Wishlist.
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Support tickets.
    Route::controller(TicketController::class)->prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/attachment/{message}', 'download')->name('attachment');
        Route::get('/{ticket}', 'show')->name('show');
        Route::post('/{ticket}/reply', 'reply')->name('reply');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Area
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', AdminProductController::class);
    Route::resource('categories', AdminCategoryController::class)->except('show');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');

    // Users / customers.
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/ban', [AdminUserController::class, 'toggleBan'])->name('users.ban');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Coupons.
    Route::get('/coupons/generate', [AdminCouponController::class, 'generate'])->name('coupons.generate');
    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
    Route::patch('/coupons/{coupon}', [AdminCouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');

    // Currencies.
    Route::get('/currencies', [AdminCurrencyController::class, 'index'])->name('currencies.index');
    Route::post('/currencies', [AdminCurrencyController::class, 'store'])->name('currencies.store');
    Route::put('/currencies/{currency}', [AdminCurrencyController::class, 'update'])->name('currencies.update');
    Route::delete('/currencies/{currency}', [AdminCurrencyController::class, 'destroy'])->name('currencies.destroy');

    // Support tickets.
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('tickets.reply');
    Route::patch('/tickets/{ticket}/close', [AdminTicketController::class, 'close'])->name('tickets.close');
    Route::patch('/tickets/{ticket}/reopen', [AdminTicketController::class, 'reopen'])->name('tickets.reopen');

    // Reports.
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::patch('/reports/{report}', [AdminReportController::class, 'update'])->name('reports.update');
    Route::delete('/reports/{report}', [AdminReportController::class, 'destroy'])->name('reports.destroy');

    // Review moderation.
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::patch('/reviews/{review}/unapprove', [AdminReviewController::class, 'unapprove'])->name('reviews.unapprove');
    Route::patch('/reviews/{review}/testimonial', [AdminReviewController::class, 'toggleTestimonial'])->name('reviews.testimonial');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Site settings.
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings/general', [AdminSettingController::class, 'updateGeneral'])->name('settings.general');
    Route::put('/settings/hero', [AdminSettingController::class, 'updateHero'])->name('settings.hero');
    Route::put('/settings/footer', [AdminSettingController::class, 'updateFooter'])->name('settings.footer');
});
