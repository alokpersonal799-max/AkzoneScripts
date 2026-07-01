<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThemeController extends Controller
{
    /**
     * Available UI themes. Each swaps the site-wide "brand" color palette
     * (and a light background tint) for an instant, cohesive re-skin.
     *
     * @var array<string, array{label: string, desc: string, colors: array<int, string>}>
     */
    public const THEMES = [
        'default' => [
            'label' => 'Default',
            'desc' => 'Clean modern blue — the classic AkzoneScripts look.',
            'colors' => ['#2563eb', '#3b82f6', '#1e3a8a'],
        ],
        'festival' => [
            'label' => 'Festival',
            'desc' => 'Vibrant magenta & purple celebration vibes — perfect for festive seasons.',
            'colors' => ['#c026d3', '#e879f9', '#701a75'],
        ],
        'prime' => [
            'label' => 'Prime Sale',
            'desc' => 'Bold orange & red energy to power up your big sale events.',
            'colors' => ['#ea580c', '#fb923c', '#7c2d12'],
        ],
    ];

    public function index(): View
    {
        return view('admin.theme.index', [
            'themes' => self::THEMES,
            'active' => Setting::get('active_theme', 'default'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'active_theme' => ['required', 'in:'.implode(',', array_keys(self::THEMES))],
        ]);

        Setting::put('active_theme', $data['active_theme'], 'theme');

        return back()->with('success', 'Theme "'.self::THEMES[$data['active_theme']]['label'].'" applied. Your whole site now uses this look.');
    }
}
