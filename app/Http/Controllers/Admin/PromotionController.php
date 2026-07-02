<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromotionController extends Controller
{
    /**
     * Show the hero promotion manager.
     */
    public function index(): View
    {
        return view('admin.promotions.index', [
            'mode' => Setting::get('promo_mode', 'off'),
            'heading' => Setting::get('promo_heading', 'Featured picks'),
            'selectedProducts' => json_decode((string) Setting::get('promo_products', '[]'), true) ?: [],
            'message' => Setting::get('promo_message', ''),
            'messageUrl' => Setting::get('promo_message_url', ''),
            'countdownProduct' => (int) Setting::get('promo_countdown_product', 0),
            'countdownLabel' => Setting::get('promo_countdown_label', 'Limited time offer'),
            'countdownUntil' => Setting::get('promo_countdown_until', ''),
            'countdownProduct2' => (int) Setting::get('promo_countdown_product_2', 0),
            'countdownLabel2' => Setting::get('promo_countdown_label_2', 'Limited time offer'),
            'countdownUntil2' => Setting::get('promo_countdown_until_2', ''),
            'promoStartsAt' => Setting::get('promo_starts_at', ''),
            'promoEndsAt' => Setting::get('promo_ends_at', ''),
            'products' => Product::published()->orderBy('title')->get(['id', 'title']),
            'coupons' => \App\Models\Coupon::orderBy('code')->pluck('code'),
            'announcementEnabled' => Setting::get('announcement_enabled', '0') === '1',
            'announcementText' => Setting::get('announcement_text', ''),
            'announcementType' => Setting::get('announcement_type', 'offer'),
            'announcementLink' => Setting::get('announcement_link', ''),
            'announcementCoupon' => Setting::get('announcement_coupon', ''),
            'announcementStartsAt' => Setting::get('announcement_starts_at', ''),
            'announcementEndsAt' => Setting::get('announcement_ends_at', ''),
            // Popup settings
            'popupEnabled' => Setting::get('popup_enabled', '0') === '1',
            'popupMode' => Setting::get('popup_mode', 'message'),
            'popupProduct' => (int) Setting::get('popup_product', 0),
            'popupHeading' => Setting::get('popup_heading', ''),
            'popupMessage' => Setting::get('popup_message', ''),
            'popupLink' => Setting::get('popup_link', ''),
            'popupLinkText' => Setting::get('popup_link_text', ''),
            'popupTimerUntil' => Setting::get('popup_timer_until', ''),
            'popupAutoCloseSeconds' => (int) Setting::get('popup_auto_close_seconds', 8),
            'popupFrequency' => Setting::get('popup_frequency', 'once'),
            'popupCoupon' => Setting::get('popup_coupon', ''),
            'popupAudience' => Setting::get('popup_audience', 'all'),
        ]);
    }

    /**
     * Persist the promotion configuration.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'promo_mode' => ['required', 'in:off,products,message,countdown'],
            'promo_heading' => ['nullable', 'string', 'max:60'],
            'promo_products' => ['nullable', 'array', 'max:4'],
            'promo_products.*' => ['integer', 'exists:products,id'],
            'promo_message' => ['nullable', 'string', 'max:255', 'required_if:promo_mode,message'],
            'promo_message_url' => ['nullable', 'url', 'max:255'],
            'promo_countdown_product' => ['nullable', 'integer', 'exists:products,id', 'required_if:promo_mode,countdown'],
            'promo_countdown_label' => ['nullable', 'string', 'max:60'],
            'promo_countdown_until' => ['nullable', 'date', 'after:now', 'required_if:promo_mode,countdown'],
            'promo_countdown_product_2' => ['nullable', 'integer', 'exists:products,id', 'required_with:promo_countdown_until_2'],
            'promo_countdown_label_2' => ['nullable', 'string', 'max:60'],
            'promo_countdown_until_2' => ['nullable', 'date', 'after:now', 'required_with:promo_countdown_product_2'],
            'announcement_text' => ['nullable', 'string', 'max:255'],
            'announcement_type' => ['nullable', 'in:info,offer,success,warning,alert'],
            'announcement_link' => ['nullable', 'url', 'max:255'],
            'announcement_coupon' => ['nullable', 'string', 'max:60'],
            'announcement_starts_at' => ['nullable', 'date'],
            'announcement_ends_at' => ['nullable', 'date'],
            'promo_starts_at' => ['nullable', 'date'],
            'promo_ends_at' => ['nullable', 'date'],
            // Popup settings
            'popup_mode' => ['nullable', 'in:message,product,offer'],
            'popup_product' => ['nullable', 'integer', 'exists:products,id'],
            'popup_heading' => ['nullable', 'string', 'max:120'],
            'popup_message' => ['nullable', 'string', 'max:500'],
            'popup_link' => ['nullable', 'url', 'max:255'],
            'popup_link_text' => ['nullable', 'string', 'max:60'],
            'popup_timer_until' => ['nullable', 'date'],
            'popup_auto_close_seconds' => ['nullable', 'integer', 'min:0', 'max:120'],
            'popup_frequency' => ['nullable', 'in:once,always'],
            'popup_coupon' => ['nullable', 'string', 'max:60'],
            'popup_audience' => ['nullable', 'in:all,new,guests'],
        ], [
            'promo_products.max' => 'You can feature at most 4 products.',
            'promo_countdown_until.after' => 'The offer end time must be in the future.',
            'promo_countdown_until_2.after' => 'The second offer end time must be in the future.',
        ]);

        if ($data['promo_mode'] === 'products' && empty($data['promo_products'])) {
            return back()->withInput()->with('error', 'Select at least one product to feature.');
        }

        Setting::put('promo_mode', $data['promo_mode'], 'promotion');
        Setting::put('promo_heading', $data['promo_heading'] ?? '', 'promotion');
        Setting::put('promo_products', json_encode(array_map('intval', $data['promo_products'] ?? [])), 'promotion');
        Setting::put('promo_message', $data['promo_message'] ?? '', 'promotion');
        Setting::put('promo_message_url', $data['promo_message_url'] ?? '', 'promotion');
        Setting::put('promo_countdown_product', (string) ($data['promo_countdown_product'] ?? ''), 'promotion');
        Setting::put('promo_countdown_label', $data['promo_countdown_label'] ?? '', 'promotion');
        Setting::put('promo_countdown_until', $data['promo_countdown_until'] ?? '', 'promotion');
        Setting::put('promo_countdown_product_2', (string) ($data['promo_countdown_product_2'] ?? ''), 'promotion');
        Setting::put('promo_countdown_label_2', $data['promo_countdown_label_2'] ?? '', 'promotion');
        Setting::put('promo_countdown_until_2', $data['promo_countdown_until_2'] ?? '', 'promotion');

        // Announcement bar (independent of the hero promo mode).
        Setting::put('announcement_enabled', $request->boolean('announcement_enabled') ? '1' : '0', 'promotion');
        Setting::put('announcement_text', $data['announcement_text'] ?? '', 'promotion');
        Setting::put('announcement_type', $data['announcement_type'] ?? 'offer', 'promotion');
        Setting::put('announcement_link', $data['announcement_link'] ?? '', 'promotion');
        Setting::put('announcement_coupon', $data['announcement_coupon'] ?? '', 'promotion');
        Setting::put('announcement_starts_at', $data['announcement_starts_at'] ?? '', 'promotion');
        Setting::put('announcement_ends_at', $data['announcement_ends_at'] ?? '', 'promotion');

        // Hero promo schedule (auto on/off window).
        Setting::put('promo_starts_at', $data['promo_starts_at'] ?? '', 'promotion');
        Setting::put('promo_ends_at', $data['promo_ends_at'] ?? '', 'promotion');

        // Promotional popup settings.
        Setting::put('popup_enabled', $request->boolean('popup_enabled') ? '1' : '0', 'promotion');
        Setting::put('popup_mode', $data['popup_mode'] ?? 'message', 'promotion');
        Setting::put('popup_product', (string) ($data['popup_product'] ?? ''), 'promotion');
        Setting::put('popup_heading', $data['popup_heading'] ?? '', 'promotion');
        Setting::put('popup_message', $data['popup_message'] ?? '', 'promotion');
        Setting::put('popup_link', $data['popup_link'] ?? '', 'promotion');
        Setting::put('popup_link_text', $data['popup_link_text'] ?? '', 'promotion');
        Setting::put('popup_timer_until', $data['popup_timer_until'] ?? '', 'promotion');
        Setting::put('popup_auto_close_seconds', (string) ($data['popup_auto_close_seconds'] ?? '8'), 'promotion');
        Setting::put('popup_frequency', $data['popup_frequency'] ?? 'once', 'promotion');
        Setting::put('popup_coupon', $data['popup_coupon'] ?? '', 'promotion');
        Setting::put('popup_audience', $data['popup_audience'] ?? 'all', 'promotion');

        $labels = ['off' => 'turned off', 'products' => 'set to featured products', 'message' => 'set to a custom message', 'countdown' => 'set to a countdown offer'];

        // Announce the updated promotion to connected Telegram bots.
        if ($data['promo_mode'] !== 'off') {
            app(\App\Services\TelegramService::class)->notify('promotion', \App\Support\TelegramMessages::promotionFromSettings());
        }

        return back()->with('success', 'Hero promotion '.$labels[$data['promo_mode']].'.');
    }
}
