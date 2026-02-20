<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security Headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        
        // Strict Content Security Policy
        $csp = "default-src 'self' https:; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; " .
               "style-src 'self' 'unsafe-inline' https: https://fonts.googleapis.com https://fonts.bunny.net; " .
               "img-src 'self' data: https:; " .
               "font-src 'self' https: data: https://fonts.bunny.net; " .
               "connect-src 'self' https: ws: wss:; " .
               "frame-ancestors 'self';";
        
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
