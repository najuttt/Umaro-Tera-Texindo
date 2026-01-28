<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class TrackGuestSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil session ID dari cookie atau buat baru
        $guestSessionId = $request->cookie('guest_session_id');
        
        if (!$guestSessionId) {
            $guestSessionId = session()->getId();
            Cookie::queue('guest_session_id', $guestSessionId, 60 * 24 * 30); // 30 hari
        }
        
        session(['guest_session_id' => $guestSessionId]);
        
        return $next($request);
    }
}