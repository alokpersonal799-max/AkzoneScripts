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
            return redirect()->to('/install');
        }

        if ($installed && $onInstaller) {
            return redirect()->to('/');
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
