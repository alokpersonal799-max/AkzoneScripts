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
            'products' => Product::published()->orderBy('title')->get(['id', 'title']),
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

        $labels = ['off' => 'turned off', 'products' => 'set to featured products', 'message' => 'set to a custom message', 'countdown' => 'set to a countdown offer'];

        return back()->with('success', 'Hero promotion '.$labels[$data['promo_mode']].'.');
    }
}
