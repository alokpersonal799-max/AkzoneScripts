<?php

use App\Http\Controllers\Admin\AdvertisementController as AdminAdvertisementController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CurrencyController as AdminCurrencyController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\StorageController as AdminStorageController;
use App\Http\Controllers\Admin\PromotionController as AdminPromotionController;
use App\Http\Controllers\Admin\SystemController as AdminSystemController;
use App\Http\Controllers\Admin\CronController as AdminCronController;
use App\Http\Controllers\Admin\ThemeController as AdminThemeController;
use App\Http\Controllers\Admin\TelegramController as AdminTelegramController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PageController;
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

// Public custom pages (About, Terms, Privacy, etc.).
Route::get('/p/{page}', [PageController::class, 'show'])->name('pages.show');

// Contact form (open to guests and members).
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Email verification link (signed, works without login).
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')->name('verification.verify');
Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');

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

    // Forgot / reset password.
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
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
    Route::get('/free-download/{product}', [DownloadController::class, 'free'])->name('products.free');

    // Order invoice PDF download.
    Route::get('/orders/{order}/invoice', [InvoiceController::class, 'download'])->middleware('throttle:10,1')->name('orders.invoice');

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

    // Notifications.
    Route::get('/notifications/{notification}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'readAll'])->name('notifications.readAll');

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
    Route::patch('/users/{user}/verify', [AdminUserController::class, 'toggleVerify'])->name('users.verify');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Custom pages.
    Route::resource('pages', AdminPageController::class)->except('show');
    Route::post('pages/preview', [AdminPageController::class, 'preview'])->name('pages.preview');

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

    // Site settings (section-based).
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::get('/settings/section/{section}', [AdminSettingController::class, 'show'])->name('settings.show');
    Route::put('/settings/general', [AdminSettingController::class, 'updateGeneral'])->name('settings.general');
    Route::put('/settings/hero', [AdminSettingController::class, 'updateHero'])->name('settings.hero');
    Route::put('/settings/footer', [AdminSettingController::class, 'updateFooter'])->name('settings.footer');
    Route::put('/settings/seo', [AdminSettingController::class, 'updateSeo'])->name('settings.seo');
    Route::put('/settings/maintenance', [AdminSettingController::class, 'updateMaintenance'])->name('settings.maintenance');
    Route::put('/settings/payments', [AdminSettingController::class, 'updatePayments'])->name('settings.payments');
    Route::put('/settings/manual', [AdminSettingController::class, 'updateManual'])->name('settings.manual');
    Route::put('/settings/homepage', [AdminSettingController::class, 'updateHomepage'])->name('settings.homepage');
    Route::put('/settings/mail', [AdminSettingController::class, 'updateMail'])->name('settings.mail');
    Route::put('/settings/auth', [AdminSettingController::class, 'updateAuth'])->name('settings.auth');
    Route::put('/settings/integrations', [AdminSettingController::class, 'updateIntegrations'])->name('settings.integrations');

    // Storage provider (where product images & files are stored).
    Route::get('/storage', [AdminStorageController::class, 'index'])->name('storage.index');
    Route::put('/storage', [AdminStorageController::class, 'update'])->name('storage.update');
    Route::post('/storage/test', [AdminStorageController::class, 'test'])->name('storage.test');

    // System information & cache tools.
    Route::get('/system', [AdminSystemController::class, 'index'])->name('system.index');
    Route::post('/system/clear-cache', [AdminSystemController::class, 'clearCache'])->name('system.cache.clear');

    // Cron jobs setup.
    Route::get('/cron', [AdminCronController::class, 'index'])->name('cron.index');
    Route::post('/system/clear-error-log', [AdminSystemController::class, 'clearErrorLog'])->name('system.error-log.clear');

    // Hero promotion manager.
    Route::get('/promotions', [AdminPromotionController::class, 'index'])->name('promotions.index');
    Route::put('/promotions', [AdminPromotionController::class, 'update'])->name('promotions.update');

    // Advertisement banner manager (AdSense / Meta / manual ads).
    Route::get('/advertisements', [AdminAdvertisementController::class, 'index'])->name('ads.index');
    Route::put('/advertisements/settings', [AdminAdvertisementController::class, 'updateSettings'])->name('ads.settings');
    Route::post('/advertisements', [AdminAdvertisementController::class, 'store'])->name('ads.store');
    Route::put('/advertisements/{ad}', [AdminAdvertisementController::class, 'update'])->name('ads.update');
    Route::delete('/advertisements/{ad}', [AdminAdvertisementController::class, 'destroy'])->name('ads.destroy');

    // Store theme.
    Route::get('/theme', [AdminThemeController::class, 'index'])->name('theme.index');
    Route::put('/theme', [AdminThemeController::class, 'update'])->name('theme.update');
    Route::put('/theme/schedule', [AdminThemeController::class, 'schedule'])->name('theme.schedule');

    // Contact messages.
    Route::get('/contacts', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::put('/contacts/settings', [AdminContactController::class, 'updateSettings'])->name('contacts.settings');
    Route::get('/contacts/{contact}', [AdminContactController::class, 'show'])->name('contacts.show');
    Route::delete('/contacts/{contact}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');

    // Telegram bot connection.
    Route::get('/telegram', [AdminTelegramController::class, 'index'])->name('tg.index');
    Route::post('/telegram', [AdminTelegramController::class, 'store'])->name('tg.store');
    Route::post('/telegram/broadcast', [AdminTelegramController::class, 'broadcast'])->name('tg.broadcast');
    Route::put('/telegram/auto-promo', [AdminTelegramController::class, 'autoPromo'])->name('tg.autopromo');
    Route::post('/telegram/auto-promo/send', [AdminTelegramController::class, 'autoPromoNow'])->name('tg.autopromo.now');
    Route::put('/telegram/daily-report', [AdminTelegramController::class, 'dailyReport'])->name('tg.dailyreport');
    Route::post('/telegram/daily-report/send', [AdminTelegramController::class, 'dailyReportNow'])->name('tg.dailyreport.now');
    Route::put('/telegram/{bot}', [AdminTelegramController::class, 'update'])->name('tg.update');
    Route::delete('/telegram/{bot}', [AdminTelegramController::class, 'destroy'])->name('tg.destroy');
    Route::post('/telegram/{bot}/test', [AdminTelegramController::class, 'test'])->name('tg.test');
});
