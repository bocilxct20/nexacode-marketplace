<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Don't cache dynamic content
        if ($request->is('admin/*') || $request->is('dashboard/*') || auth()->check()) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            return $response;
        }

        // Cache static assets
        if ($request->is('build/*') || $request->is('storage/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            
            // Generate ETag
            $etag = md5($response->getContent());
            $response->headers->set('ETag', $etag);
            
            // Check if client has cached version
            if ($request->header('If-None-Match') === $etag) {
                return response('', 304)->withHeaders($response->headers->all());
            }
            
            return $response;
        }

        // Cache public pages
        if ($request->is('/') || $request->is('items') || $request->is('items/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=300'); // 5 minutes
            
            // Generate ETag
            $etag = md5($response->getContent());
            $response->headers->set('ETag', $etag);
            
            // Set Last-Modified
            $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
            
            return $response;
        }

        return $response;
    }
}
