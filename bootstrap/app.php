<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'onboarding.completed' => \App\Http\Middleware\EnsureOnboardingCompleted::class,
            'seller' => \App\Http\Middleware\EnsureSeller::class,
        ]);

        // Webhook Midtrans dipanggil langsung oleh server Midtrans:
        // tidak punya sesi Laravel, jadi harus dikecualikan dari verifikasi CSRF.
        $middleware->validateCsrfTokens(except: ['midtrans/notification']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
