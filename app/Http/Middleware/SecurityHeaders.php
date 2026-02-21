<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Apply security headers to every response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // X-Content-Type-Options
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-Frame-Options
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // X-XSS-Protection (legacy browsers)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // HTTP Strict Transport Security (HTTPS only, 1 year)
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        // Referrer-Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions-Policy
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Content-Security-Policy
        $csp = "default-src 'self' https:; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; " .
               "style-src 'self' 'unsafe-inline' https: https://fonts.googleapis.com https://fonts.bunny.net; " .
               "img-src 'self' data: https: blob:; " .
               "font-src 'self' https: data: https://fonts.bunny.net; " .
               "connect-src 'self' https: ws: wss:; " .
               "media-src 'self' https:; " .
               "frame-ancestors 'self';";
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
