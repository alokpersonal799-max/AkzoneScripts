<?php

namespace App\Http\Middleware;

use App\Services\TelegramService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoTelegramPromo
{
    public function __construct(protected TelegramService $telegram)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Run the time-based auto promotion after the response is sent, so it never
     * slows down the page. Acts as a lightweight "cron-less" scheduler.
     */
    public function terminate(Request $request, Response $response): void
    {
        // Only consider GET page views (skip assets, AJAX, and admin pages).
        if (! $request->isMethod('GET') || $request->ajax() || $request->is('admin*')) {
            return;
        }

        try {
            $this->telegram->runAutoPromo();
        } catch (\Throwable $e) {
            // Never let promo dispatch affect the request lifecycle.
        }
    }
}
