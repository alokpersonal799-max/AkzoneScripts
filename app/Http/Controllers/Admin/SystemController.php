<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class SystemController extends Controller
{
    /**
     * Display the System Information page.
     */
    public function index(Request $request): View
    {
        return view('admin.system.index', [
            'app' => [
                'Name' => setting('site_name', config('app.name')),
                'Version' => config('marketplace.version', '1.0.0'),
                'Laravel Version' => app()->version(),
                'Timezone' => config('app.timezone', 'UTC'),
            ],
            'server' => [
                'Software' => $request->server('SERVER_SOFTWARE') ?: 'Unknown',
                'PHP Version' => PHP_VERSION,
                'IP Address' => $request->server('SERVER_ADDR') ?: $request->ip(),
                'Protocol' => $request->server('SERVER_PROTOCOL') ?: ($request->isSecure() ? 'HTTPS' : 'HTTP'),
                'HTTP Host' => $request->getHttpHost(),
                'Port' => (string) $request->getPort(),
            ],
        ]);
    }

    /**
     * Flush all framework caches, compiled views and the log file.
     */
    public function clearCache(): RedirectResponse
    {
        $cleared = [];

        foreach (['view:clear', 'cache:clear', 'route:clear', 'config:clear'] as $command) {
            try {
                Artisan::call($command);
                $cleared[] = $command;
            } catch (\Throwable $e) {
                // Ignore individual command failures so one bad cache doesn't block the rest.
            }
        }

        // Truncate the default Laravel log file, if present.
        try {
            $log = storage_path('logs/laravel.log');
            if (File::exists($log)) {
                File::put($log, '');
                $cleared[] = 'error logs';
            }
        } catch (\Throwable $e) {
            // Non-fatal.
        }

        return back()->with('success', 'Caches cleared: '.implode(', ', $cleared).'.');
    }
}
