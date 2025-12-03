<?php

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
        // Daftarkan middleware alias
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // Middleware groups
        $middleware->web(append: [
            // Tambahkan middleware web jika diperlukan
        ]);

        $middleware->api(append: [
            // Tambahkan middleware api jika diperlukan
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();