<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompressResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Check if compression is enabled
        if (!config('performance.compression.enabled')) {
            return $response;
        }

        // Check if client accepts gzip
        $acceptEncoding = $request->header('Accept-Encoding', '');
        if (!str_contains($acceptEncoding, 'gzip')) {
            return $response;
        }

        // Get response content
        $content = $response->getContent();
        
        // Check minimum size
        if (strlen($content) < config('performance.compression.min_size', 1024)) {
            return $response;
        }

        // Check content type
        $contentType = $response->headers->get('Content-Type', '');
        $allowedTypes = config('performance.compression.types', []);
        
        $isAllowed = false;
        foreach ($allowedTypes as $type) {
            if (str_contains($contentType, $type)) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            return $response;
        }

        // Compress content
        $compressed = gzencode($content, config('performance.compression.level', 6));
        
        if ($compressed === false) {
            return $response;
        }

        // Set compressed content and headers
        $response->setContent($compressed);
        $response->headers->set('Content-Encoding', 'gzip');
        $response->headers->set('Content-Length', strlen($compressed));
        $response->headers->remove('Content-MD5'); // MD5 would be invalid after compression

        return $response;
    }
}
