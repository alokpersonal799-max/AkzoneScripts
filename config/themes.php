<?php

/*
|--------------------------------------------------------------------------
| Store Themes
|--------------------------------------------------------------------------
|
| Each theme swaps the site-wide "brand" color palette and can enable a
| decorative effect (confetti / snow / hearts / SALE ribbon). Used by the
| admin Theme page, the head palette injection, and the effects overlay.
|
| effect: none | confetti | snow | hearts | ribbon
|
*/

return [

    'default' => [
        'label' => 'Default',
        'desc' => 'Clean modern blue — the classic AkzoneScripts look.',
        'effect' => 'none',
        'swatch' => ['#2563eb', '#3b82f6', '#1e3a8a'],
        'brand' => ['50' => '#eff5ff', '100' => '#dbe8fe', '200' => '#bfd7fe', '300' => '#93bbfd', '400' => '#609afa', '500' => '#3b82f6', '600' => '#2563eb', '700' => '#1d4ed8', '800' => '#1e40af', '900' => '#1e3a8a'],
    ],

    'emerald' => [
        'label' => 'Emerald',
        'desc' => 'Fresh, calm green — great for an eco or premium vibe.',
        'effect' => 'none',
        'swatch' => ['#059669', '#34d399', '#064e3b'],
        'brand' => ['50' => '#ecfdf5', '100' => '#d1fae5', '200' => '#a7f3d0', '300' => '#6ee7b7', '400' => '#34d399', '500' => '#10b981', '600' => '#059669', '700' => '#047857', '800' => '#065f46', '900' => '#064e3b'],
    ],

    'ocean' => [
        'label' => 'Ocean',
        'desc' => 'Cool cyan & teal — bright, techy and refreshing.',
        'effect' => 'none',
        'swatch' => ['#0891b2', '#22d3ee', '#164e63'],
        'brand' => ['50' => '#ecfeff', '100' => '#cffafe', '200' => '#a5f3fc', '300' => '#67e8f9', '400' => '#22d3ee', '500' => '#06b6d4', '600' => '#0891b2', '700' => '#0e7490', '800' => '#155e75', '900' => '#164e63'],
    ],

    'sunset' => [
        'label' => 'Sunset',
        'desc' => 'Warm golden amber — cozy and inviting.',
        'effect' => 'none',
        'swatch' => ['#d97706', '#fbbf24', '#78350f'],
        'brand' => ['50' => '#fffbeb', '100' => '#fef3c7', '200' => '#fde68a', '300' => '#fcd34d', '400' => '#fbbf24', '500' => '#f59e0b', '600' => '#d97706', '700' => '#b45309', '800' => '#92400e', '900' => '#78350f'],
    ],

    'midnight' => [
        'label' => 'Midnight',
        'desc' => 'Deep indigo — sleek, modern and premium.',
        'effect' => 'none',
        'swatch' => ['#4f46e5', '#818cf8', '#312e81'],
        'brand' => ['50' => '#eef2ff', '100' => '#e0e7ff', '200' => '#c7d2fe', '300' => '#a5b4fc', '400' => '#818cf8', '500' => '#6366f1', '600' => '#4f46e5', '700' => '#4338ca', '800' => '#3730a3', '900' => '#312e81'],
    ],

    'festival' => [
        'label' => 'Festival',
        'desc' => 'Vibrant magenta & purple with falling confetti — party time!',
        'effect' => 'confetti',
        'swatch' => ['#c026d3', '#e879f9', '#701a75'],
        'brand' => ['50' => '#fdf4ff', '100' => '#fae8ff', '200' => '#f5d0fe', '300' => '#f0abfc', '400' => '#e879f9', '500' => '#d946ef', '600' => '#c026d3', '700' => '#a21caf', '800' => '#86198f', '900' => '#701a75'],
    ],

    'christmas' => [
        'label' => 'Christmas',
        'desc' => 'Festive red with gentle falling snow. Ho ho ho! ❄',
        'effect' => 'snow',
        'swatch' => ['#dc2626', '#f87171', '#7f1d1d'],
        'brand' => ['50' => '#fef2f2', '100' => '#fee2e2', '200' => '#fecaca', '300' => '#fca5a5', '400' => '#f87171', '500' => '#ef4444', '600' => '#dc2626', '700' => '#b91c1c', '800' => '#991b1b', '900' => '#7f1d1d'],
    ],

    'valentine' => [
        'label' => 'Valentine',
        'desc' => 'Romantic pink with floating hearts. 💖',
        'effect' => 'hearts',
        'swatch' => ['#db2777', '#f472b6', '#831843'],
        'brand' => ['50' => '#fdf2f8', '100' => '#fce7f3', '200' => '#fbcfe8', '300' => '#f9a8d4', '400' => '#f472b6', '500' => '#ec4899', '600' => '#db2777', '700' => '#be185d', '800' => '#9d174d', '900' => '#831843'],
    ],

    'prime' => [
        'label' => 'Prime Sale',
        'desc' => 'Bold orange & red with a corner SALE ribbon — drive big sales!',
        'effect' => 'ribbon',
        'swatch' => ['#ea580c', '#fb923c', '#7c2d12'],
        'brand' => ['50' => '#fff7ed', '100' => '#ffedd5', '200' => '#fed7aa', '300' => '#fdba74', '400' => '#fb923c', '500' => '#f97316', '600' => '#ea580c', '700' => '#c2410c', '800' => '#9a3412', '900' => '#7c2d12'],
    ],

];
