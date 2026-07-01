<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class PwaController extends Controller
{
    /**
     * Dynamic web app manifest.
     */
    public function manifest(): JsonResponse
    {
        $name = setting('site_name', config('app.name', 'AkzoneScripts'));

        return response()->json([
            'name' => $name,
            'short_name' => Str::limit($name, 12, ''),
            'description' => setting('seo_description', config('marketplace.tagline', 'Digital marketplace')),
            'start_url' => url('/'),
            'scope' => url('/'),
            'display' => 'standalone',
            'orientation' => 'portrait',
            'background_color' => '#ffffff',
            'theme_color' => '#2563eb',
            'icons' => [
                ['src' => route('pwa.icon'), 'sizes' => '192x192', 'type' => 'image/svg+xml', 'purpose' => 'any maskable'],
                ['src' => route('pwa.icon'), 'sizes' => '512x512', 'type' => 'image/svg+xml', 'purpose' => 'any maskable'],
            ],
        ])->withHeaders(['Content-Type' => 'application/manifest+json']);
    }

    /**
     * A scalable SVG app icon (site logo if uploaded, else brand initial).
     */
    public function icon(): Response
    {
        $initial = strtoupper(mb_substr(setting('site_name', 'A') ?: 'A', 0, 1));
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512">'
            .'<rect width="512" height="512" rx="112" fill="#2563eb"/>'
            .'<text x="256" y="256" dy=".12em" font-family="Arial,sans-serif" font-size="270" font-weight="800" fill="#fff" text-anchor="middle" dominant-baseline="middle">'.htmlspecialchars($initial, ENT_QUOTES).'</text></svg>';

        return response($svg, 200, ['Content-Type' => 'image/svg+xml', 'Cache-Control' => 'public, max-age=86400']);
    }
}
