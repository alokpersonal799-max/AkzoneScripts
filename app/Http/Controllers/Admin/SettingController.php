<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Show the settings screen (general / hero / footer tabs in one page).
     */
    public function index(): View
    {
        return view('admin.settings.index', [
            'settings' => Setting::cached(),
        ]);
    }

    /**
     * Persist general site settings (name, logo, socials, announcement).
     */
    public function updateGeneral(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'support_email' => ['required', 'email', 'max:255'],
            'social_twitter' => ['nullable', 'string', 'max:255'],
            'social_github' => ['nullable', 'string', 'max:255'],
            'social_discord' => ['nullable', 'string', 'max:255'],
            'social_facebook' => ['nullable', 'string', 'max:255'],
            'announcement_enabled' => ['nullable', 'boolean'],
            'announcement_text' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $existing = Setting::get('site_logo');
            if ($existing) {
                Storage::disk('public')->delete($existing);
            }
            Setting::put('site_logo', $request->file('logo')->store('branding', 'public'), 'general');
        }

        Setting::put('site_name', $data['site_name'], 'general');
        Setting::put('support_email', $data['support_email'], 'general');
        Setting::put('social_twitter', $data['social_twitter'] ?? '#', 'general');
        Setting::put('social_github', $data['social_github'] ?? '#', 'general');
        Setting::put('social_discord', $data['social_discord'] ?? '#', 'general');
        Setting::put('social_facebook', $data['social_facebook'] ?? '#', 'general');
        Setting::put('announcement_enabled', $request->boolean('announcement_enabled') ? '1' : '0', 'general');
        Setting::put('announcement_text', $data['announcement_text'] ?? '', 'general');

        return back()->with('success', 'General settings saved.');
    }

    /**
     * Persist the editable hero section copy.
     */
    public function updateHero(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'hero_badge' => ['nullable', 'string', 'max:255'],
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_highlight' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($data as $key => $value) {
            Setting::put($key, $value ?? '', 'hero');
        }

        return back()->with('success', 'Hero section updated.');
    }

    /**
     * Persist footer content.
     */
    public function updateFooter(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'footer_about' => ['nullable', 'string', 'max:1000'],
            'footer_copyright' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($data as $key => $value) {
            Setting::put($key, $value ?? '', 'footer');
        }

        return back()->with('success', 'Footer updated.');
    }
}
