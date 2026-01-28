<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('app.login_secret');

        if ($request->session()->has('login_redirect')) {
            return $next($request);
        }

        if ($request->query('token') !== $secret) {
            abort(404);
        }

        return $next($request);
    }

}
