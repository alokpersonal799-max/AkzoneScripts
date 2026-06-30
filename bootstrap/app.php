<?php

use App\Http\Middleware\AutoTelegramPromo;
use App\Http\Middleware\EnsureInstalled;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Gate every web request behind the installer until setup is complete.
        $middleware->web(append: [
            EnsureInstalled::class,
            AutoTelegramPromo::class,
        ]);

        // Register the route middleware alias used to guard admin-only routes.
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
