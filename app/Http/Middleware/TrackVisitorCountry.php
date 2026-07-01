<?php

namespace App\Http\Middleware;

use App\Models\CountryView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitorCountry
{
    /**
     * Record the visitor's country for storefront GET page views.
     *
     * Country is resolved from common CDN/proxy headers (Cloudflare's
     * CF-IPCountry, etc.). If no header is present (e.g. plain local host),
     * tracking is simply skipped — it never affects the request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && ! $request->ajax()) {
            $code = $request->header('CF-IPCountry')
                ?? $request->header('X-Country-Code')
                ?? $request->server('GEOIP_COUNTRY_CODE');

            if ($code && $code !== 'XX') {
                CountryView::record($code);
            }
        }

        return $next($request);
    }
}
