<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Content Security Policy (Updated to allow Vite's local dev server on port 5173)
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://127.0.0.1:5173 http://localhost:5173; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com http://127.0.0.1:5173 http://localhost:5173; " .
               "font-src 'self' https://fonts.gstatic.com; " .
               "img-src 'self' data: https: http://127.0.0.1:5173 http://localhost:5173; " .
               "connect-src 'self' ws: wss: http://127.0.0.1:5173 http://localhost:5173;"; 

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}