<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $config = config('security.headers', []);

        // Content Security Policy
        if (isset($config['csp'])) {
            $response->headers->set('Content-Security-Policy', $config['csp']);
        }

        // HTTP Strict Transport Security
        if (isset($config['hsts'])) {
            $response->headers->set('Strict-Transport-Security', $config['hsts']);
        }

        // X-Frame-Options
        if (isset($config['x_frame_options'])) {
            $response->headers->set('X-Frame-Options', $config['x_frame_options']);
        }

        // X-Content-Type-Options
        if (isset($config['x_content_type_options'])) {
            $response->headers->set('X-Content-Type-Options', $config['x_content_type_options']);
        }

        // Referrer-Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions-Policy
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // X-XSS-Protection (legacy but still useful)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        return $response;
    }
}
