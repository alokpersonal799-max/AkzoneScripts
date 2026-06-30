<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstalled
{
    /**
     * Gate the whole application behind the web installer.
     *
     * - If the app is NOT installed yet, every request (except the installer
     *   itself and the health check) is redirected to /install.
     * - If the app IS installed, the installer routes are locked and redirect
     *   back to the homepage so it can never be run twice.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $installed = $this->isInstalled();
        $onInstaller = $request->is('install', 'install/*');
        $allowed = $request->is('up'); // Laravel health check endpoint

        if (! $installed && ! $onInstaller && ! $allowed) {
            return redirect()->route('install.requirements');
        }

        if ($installed && $onInstaller) {
            return redirect()->route('home');
        }

        // Maintenance mode: block non-admins (except auth + admin + install routes).
        if ($installed && \App\Models\Setting::get('maintenance_enabled') === '1') {
            $user = $request->user();
            $bypass = $request->is('admin', 'admin/*', 'login', 'logout', 'install', 'install/*') || $allowed;

            if (! $bypass && (! $user || ! $user->isAdmin())) {
                return response()->view('maintenance', [
                    'message' => \App\Models\Setting::get('maintenance_message'),
                ], 503);
            }
        }

        return $next($request);
    }

    /**
     * The application is considered installed once the lock file exists.
     */
    public static function isInstalled(): bool
    {
        return file_exists(storage_path('installed'));
    }
}
