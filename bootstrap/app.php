<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\CheckMaintenanceMode::class);

        // Security middleware
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->append(\App\Http\Middleware\SuppressVitePreload::class);
        
        // Performance middleware
        $middleware->append(\App\Http\Middleware\CompressResponse::class);
        $middleware->append(\App\Http\Middleware\CacheControl::class);
        $middleware->append(\App\Http\Middleware\AffiliateTracker::class);
        
        // Middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            '2fa' => \App\Http\Middleware\RequireTwoFactor::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'midtrans/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
