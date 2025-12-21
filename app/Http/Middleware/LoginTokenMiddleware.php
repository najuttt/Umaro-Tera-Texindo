<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Token rahasia (ubah sesukamu)
        $secret = config('app.login_secret');

        // Ambil token dari query
        if ($request->query('token') !== $secret) {
            abort(404); // pura-pura halaman ga ada
        }

        return $next($request);
    }
}
