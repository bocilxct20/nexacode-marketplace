<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuppressVitePreload
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Remove the 'Link' header that Vite adds for preloading
        // This resolves the "preloaded but not used" warning in Chrome
        if ($response->headers->has('Link')) {
            $header = $response->headers->get('Link');
            
            // If it's a preload link for Vite assets, we remove or filter it
            if (str_contains($header, 'rel=preload') && str_contains($header, 'build/assets')) {
                $response->headers->remove('Link');
            }
        }

        return $response;
    }
}
