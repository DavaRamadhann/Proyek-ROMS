<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Deteksi environment
        $isDevelopment = app()->environment('local');

        // CSP Policy berdasarkan environment
        if ($isDevelopment) {
            // Development: Izinkan unsafe-eval untuk Vite HMR + CDN eksternal
            // Menggunakan scheme wildcard (http: https: ws:) untuk menghindari masalah sintaks IPv6 ([::1]) di beberapa browser
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-eval' 'unsafe-inline' https://cdn.jsdelivr.net https://accounts.google.com https://www.gstatic.com http: https:; " .
                   "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com http: https:; " .
                   "img-src 'self' data: https: http:; " .
                   "font-src 'self' data: https://cdn.jsdelivr.net https://fonts.gstatic.com http: https:; " .
                   "connect-src 'self' https://cdn.jsdelivr.net ws://localhost:* ws://127.0.0.1:* http://localhost:* http://127.0.0.1:* ws: http: https:; " .
                   "frame-src https://accounts.google.com;";
            
            // Development: Gunakan Report-Only untuk tidak blocking dan kurangi warning
            $response->headers->set('Content-Security-Policy-Report-Only', $csp);
        } else {
            // Production: Strict CSP + CDN eksternal (Enforce mode)
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-eval' https://cdn.jsdelivr.net https://accounts.google.com https://www.gstatic.com; " .
                   "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; " .
                   "img-src 'self' data: https:; " .
                   "font-src 'self' data: https://cdn.jsdelivr.net https://fonts.gstatic.com; " .
                   "connect-src 'self' https://cdn.jsdelivr.net; " .
                   "frame-src https://accounts.google.com;";
            
            // Production: Enforce CSP (blocking mode)
            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }
}
