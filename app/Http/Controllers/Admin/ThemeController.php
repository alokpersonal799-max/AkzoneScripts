<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThemeController extends Controller
{
    public function index(): View
    {
        return view('admin.theme.index', [
            'themes' => config('themes', []),
            'active' => Setting::get('active_theme', 'default'),
            'schedule' => [
                'enabled' => Setting::get('theme_schedule_enabled', '0') === '1',
                'theme' => Setting::get('theme_schedule_theme', ''),
                'start' => Setting::get('theme_schedule_start', ''),
                'end' => Setting::get('theme_schedule_end', ''),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $keys = array_keys(config('themes', []));

        $data = $request->validate([
            'active_theme' => ['required', 'in:'.implode(',', $keys)],
        ]);

        Setting::put('active_theme', $data['active_theme'], 'theme');

        $label = config('themes.'.$data['active_theme'].'.label', $data['active_theme']);

        return back()->with('success', 'Theme "'.$label.'" applied. Your whole site now uses this look.');
    }

    /**
     * Save the scheduled theme window (auto-activates a theme between two dates).
     */
    public function schedule(Request $request): RedirectResponse
    {
        $keys = array_keys(config('themes', []));

        $data = $request->validate([
            'theme_schedule_theme' => ['nullable', 'in:'.implode(',', $keys), 'required_with:theme_schedule_start,theme_schedule_end'],
            'theme_schedule_start' => ['nullable', 'date'],
            'theme_schedule_end' => ['nullable', 'date', 'after:theme_schedule_start'],
        ], [
            'theme_schedule_end.after' => 'The end date must be after the start date.',
        ]);

        Setting::put('theme_schedule_enabled', $request->boolean('theme_schedule_enabled') ? '1' : '0', 'theme');
        Setting::put('theme_schedule_theme', $data['theme_schedule_theme'] ?? '', 'theme');
        Setting::put('theme_schedule_start', $data['theme_schedule_start'] ?? '', 'theme');
        Setting::put('theme_schedule_end', $data['theme_schedule_end'] ?? '', 'theme');

        return back()->with('success', 'Theme schedule saved.');
    }
}
