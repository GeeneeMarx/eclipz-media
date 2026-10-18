<?php

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Search-engine files (/sitemap.xml and /robots.txt). No session or cookies needed.
            Route::group([], base_path('routes/seo.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Rejects requests whose Host header is not ours. Laravel skips this check in
        // local development and in tests, so 127.0.0.1 and localhost keep working.
        $middleware->trustHosts(at: fn () => array_map(
            fn (string $host) => '^'.preg_quote($host).'$',
            (array) config('security.trusted_hosts', [])
        ));

        // Security headers on every public response. The admin panel adds the same
        // middleware in its own panel configuration.
        $middleware->web(append: [SecurityHeaders::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();