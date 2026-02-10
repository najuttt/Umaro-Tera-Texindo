<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\TrackGuestSession; // ✅ IMPORT DULU

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // ✅ TAMBAHKAN INI - BIAR JALAN DI SEMUA REQUEST WEB
        $middleware->web(append: [
            TrackGuestSession::class,
        ]);

        // ✅ ALIAS MIDDLEWARE KAMU (AMAN, JANGAN DIHAPUS)
        $middleware->alias([
            'role'        => \App\Http\Middleware\RoleMiddleware::class,
            'login.token' => \App\Http\Middleware\LoginTokenMiddleware::class,
        ]);

        // 🔥 INI KUNCI UTAMANYA
        $middleware->redirectGuestsTo(function (Request $request) {
            // 🟢 KALAU DARI CHECKOUT → BALIK KE CHECKOUT
            if ($request->is('checkout*')) {
                return route('checkout.page');
            }
            // 🛡️ DEFAULT → LOGIN BIASA
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();