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
            'products' => Product::published()->orderBy('title')->get(),
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
     * Persist the promotion configuration. Each section (hero / announcement /
     * popup) saves independently so one section can never block another.
     */
    public function update(Request $request): RedirectResponse
    {
        $section = $request->input('section', 'all');

        if (in_array($section, ['hero', 'all'], true)) {
            $this->saveHero($request);
        }
        if (in_array($section, ['announcement', 'all'], true)) {
            $this->saveAnnouncement($request);
        }
        if (in_array($section, ['popup', 'all'], true)) {
            $this->savePopup($request);
        }

        $labels = ['hero' => 'Hero promotion', 'announcement' => 'Announcement bar', 'popup' => 'Popup', 'all' => 'Promotions'];

        return back()->with('success', ($labels[$section] ?? 'Promotion').' saved.');
    }

    protected function saveHero(Request $request): void
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
            'promo_countdown_until' => ['nullable', 'date', 'required_if:promo_mode,countdown'],
            'promo_countdown_product_2' => ['nullable', 'integer', 'exists:products,id', 'required_with:promo_countdown_until_2'],
            'promo_countdown_label_2' => ['nullable', 'string', 'max:60'],
            'promo_countdown_until_2' => ['nullable', 'date', 'required_with:promo_countdown_product_2'],
            'promo_starts_at' => ['nullable', 'date'],
            'promo_ends_at' => ['nullable', 'date'],
        ], ['promo_products.max' => 'You can feature at most 4 products.']);

        // Featured mode with nothing selected simply falls back to a normal hero.
        if ($data['promo_mode'] === 'products' && empty($data['promo_products'])) {
            $data['promo_mode'] = 'off';
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
        Setting::put('promo_starts_at', $data['promo_starts_at'] ?? '', 'promotion');
        Setting::put('promo_ends_at', $data['promo_ends_at'] ?? '', 'promotion');

        if ($data['promo_mode'] !== 'off') {
            app(\App\Services\TelegramService::class)->notify('promotion', \App\Support\TelegramMessages::promotionFromSettings());
        }
    }

    protected function saveAnnouncement(Request $request): void
    {
        $data = $request->validate([
            'announcement_text' => ['nullable', 'string', 'max:255'],
            'announcement_type' => ['nullable', 'in:info,offer,success,warning,alert'],
            'announcement_link' => ['nullable', 'url', 'max:255'],
            'announcement_coupon' => ['nullable', 'string', 'max:60'],
            'announcement_starts_at' => ['nullable', 'date'],
            'announcement_ends_at' => ['nullable', 'date'],
        ]);

        Setting::put('announcement_enabled', $request->boolean('announcement_enabled') ? '1' : '0', 'promotion');
        Setting::put('announcement_text', $data['announcement_text'] ?? '', 'promotion');
        Setting::put('announcement_type', $data['announcement_type'] ?? 'offer', 'promotion');
        Setting::put('announcement_link', $data['announcement_link'] ?? '', 'promotion');
        Setting::put('announcement_coupon', $data['announcement_coupon'] ?? '', 'promotion');
        Setting::put('announcement_starts_at', $data['announcement_starts_at'] ?? '', 'promotion');
        Setting::put('announcement_ends_at', $data['announcement_ends_at'] ?? '', 'promotion');
    }

    protected function savePopup(Request $request): void
    {
        $data = $request->validate([
            'popup_mode' => ['nullable', 'in:message,product,offer'],
            'popup_heading_msg' => ['nullable', 'string', 'max:120'],
            'popup_message_msg' => ['nullable', 'string', 'max:500'],
            'popup_product_prod' => ['nullable', 'integer', 'exists:products,id'],
            'popup_message_prod' => ['nullable', 'string', 'max:500'],
            'popup_product_off' => ['nullable', 'integer', 'exists:products,id'],
            'popup_heading_off' => ['nullable', 'string', 'max:120'],
            'popup_message_off' => ['nullable', 'string', 'max:500'],
            'popup_link' => ['nullable', 'url', 'max:255'],
            'popup_link_text' => ['nullable', 'string', 'max:60'],
            'popup_timer_until' => ['nullable', 'date'],
            'popup_auto_close_seconds' => ['nullable', 'integer', 'min:0', 'max:120'],
            'popup_frequency' => ['nullable', 'in:once,always'],
            'popup_coupon' => ['nullable', 'string', 'max:60'],
            'popup_audience' => ['nullable', 'in:all,new,guests'],
        ]);

        $mode = $data['popup_mode'] ?? 'message';

        // Resolve the active mode's heading / message / product from static field names.
        $heading = $mode === 'offer' ? ($data['popup_heading_off'] ?? '') : ($data['popup_heading_msg'] ?? '');
        $message = match ($mode) {
            'product' => $data['popup_message_prod'] ?? '',
            'offer' => $data['popup_message_off'] ?? '',
            default => $data['popup_message_msg'] ?? '',
        };
        $product = match ($mode) {
            'product' => $data['popup_product_prod'] ?? '',
            'offer' => $data['popup_product_off'] ?? '',
            default => '',
        };

        Setting::put('popup_enabled', $request->boolean('popup_enabled') ? '1' : '0', 'promotion');
        Setting::put('popup_mode', $mode, 'promotion');
        Setting::put('popup_product', (string) $product, 'promotion');
        Setting::put('popup_heading', $heading, 'promotion');
        Setting::put('popup_message', $message, 'promotion');
        Setting::put('popup_link', $data['popup_link'] ?? '', 'promotion');
        Setting::put('popup_link_text', $data['popup_link_text'] ?? '', 'promotion');
        Setting::put('popup_timer_until', $data['popup_timer_until'] ?? '', 'promotion');
        Setting::put('popup_auto_close_seconds', (string) ($data['popup_auto_close_seconds'] ?? '8'), 'promotion');
        Setting::put('popup_frequency', $data['popup_frequency'] ?? 'once', 'promotion');
        Setting::put('popup_coupon', $data['popup_coupon'] ?? '', 'promotion');
        Setting::put('popup_audience', $data['popup_audience'] ?? 'all', 'promotion');
    }
}
