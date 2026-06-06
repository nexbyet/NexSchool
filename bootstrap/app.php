<?php

// NexSchool - Application Bootstrap
// Laravel 12 application configuration
// API routing enabled for RESTful endpoints

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckInstall;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckLicense;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',      // Web routes (session auth)
        api: __DIR__.'/../routes/api.php',        // API routes (token auth via Sanctum)
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(prepend: [
            CheckInstall::class,
        ]);

        $middleware->alias([
            'role' => CheckRole::class,
            'license' => CheckLicense::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
