<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdvertisementController extends Controller
{
    /**
     * Per-page toggle keys the advertisement banner supports.
     *
     * @var array<int, string>
     */
    protected array $pageKeys = [
        'marketplace', 'cart', 'checkout', 'dashboard', 'purchases', 'wishlist', 'support',
        'home_free', 'home_reviews', 'pages', 'contact', 'services',
    ];

    /**
     * Show the advertisement manager (settings + manual ads).
     */
    public function index(): View
    {
        $pages = [];
        foreach ($this->pageKeys as $page) {
            $pages[$page] = Setting::get('ads_page_'.$page, '1') === '1';
        }

        return view('admin.advertisements.index', [
            'ads' => Advertisement::orderBy('sort_order')->get(),
            'enabled' => Setting::get('ads_enabled', '0') === '1',
            'adsenseCode' => Setting::get('ads_adsense_code', ''),
            'metaCode' => Setting::get('ads_meta_code', ''),
            'layout' => (int) Setting::get('ads_layout', 4),
            'pages' => $pages,
        ]);
    }

    /**
     * Persist the advertisement settings (global toggle, codes, layout, pages).
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ads_adsense_code' => ['nullable', 'string', 'max:5000'],
            'ads_meta_code' => ['nullable', 'string', 'max:5000'],
            'ads_layout' => ['nullable', 'in:1,2,3,4,6,8'],
        ]);

        Setting::put('ads_enabled', $request->boolean('ads_enabled') ? '1' : '0', 'ads');
        Setting::put('ads_adsense_code', $data['ads_adsense_code'] ?? '', 'ads');
        Setting::put('ads_meta_code', $data['ads_meta_code'] ?? '', 'ads');
        Setting::put('ads_layout', $data['ads_layout'] ?? '4', 'ads');

        foreach ($this->pageKeys as $page) {
            Setting::put('ads_page_'.$page, $request->boolean('ads_page_'.$page) ? '1' : '0', 'ads');
        }

        return back()->with('success', 'Advertisement settings saved.');
    }

    /**
     * Create a new manual advertisement.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateAd($request);

        if (! $request->hasFile('image') && empty($data['image_url'])) {
            return back()->withInput()->with('error', 'Please upload an image or provide an image URL.');
        }

        $data['image_path'] = $request->hasFile('image')
            ? $request->file('image')->store('ads', 'public')
            : null;
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        unset($data['image']);

        Advertisement::create($data);

        return back()->with('success', 'Advertisement added.');
    }

    /**
     * Update an existing advertisement.
     */
    public function update(Request $request, Advertisement $ad): RedirectResponse
    {
        $data = $this->validateAd($request);

        if ($request->hasFile('image')) {
            if ($ad->image_path) {
                Storage::disk('public')->delete($ad->image_path);
            }
            $data['image_path'] = $request->file('image')->store('ads', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        unset($data['image']);

        $ad->update($data);

        return back()->with('success', 'Advertisement updated.');
    }

    /**
     * Delete an advertisement (and its uploaded image).
     */
    public function destroy(Advertisement $ad): RedirectResponse
    {
        if ($ad->image_path) {
            Storage::disk('public')->delete($ad->image_path);
        }

        $ad->delete();

        return back()->with('success', 'Advertisement deleted.');
    }

    /**
     * Shared validation rules for storing/updating an ad.
     *
     * @return array<string, mixed>
     */
    protected function validateAd(Request $request): array
    {
        return $request->validate([
            'title' => ['nullable', 'string', 'max:120'],
            'image' => ['nullable', 'image', 'max:2048'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'link_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
