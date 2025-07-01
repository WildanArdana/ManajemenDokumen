<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware; // <--- PASTIKAN BARIS INI ADA DI ATAS

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => AdminMiddleware::class, // <--- PASTIKAN INI ADALAH CARA PENULISANNYA
        ]);

        // Anda mungkin juga memiliki middleware lain di sini
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();