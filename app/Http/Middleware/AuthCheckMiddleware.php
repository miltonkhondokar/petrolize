<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AuthCheckMiddleware
{
    public function handle($request, Closure $next)
    {
        // Allow login routes without authentication
        if ($request->is('login', 'register')) {
            if (Auth::check()) {
                return redirect()->route('/');
            }
            return $next($request);
        }

        // Require authentication for everything else (WEB ONLY)
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $response = $next($request);

        // Prevent caching of authenticated pages
        if (!$response instanceof BinaryFileResponse) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}
