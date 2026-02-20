<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AffiliateTracker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('ref')) {
            $refCode = $request->query('ref');
            
            // Set cookie for 30 days (43200 minutes)
            cookie()->queue('nexacode_affiliate', $refCode, 43200);

            // Log the click
            $this->logClick($request, $refCode);
        }

        return $next($request);
    }

    /**
     * Log the affiliate click
     */
    protected function logClick(Request $request, string $refCode): void
    {
        $affiliate = \App\Models\User::where('affiliate_code', $refCode)->first();
        if (!$affiliate) return;

        $ip = $request->ip();

        // Simple debounce: don't log the same IP for the same affiliate within 1 hour
        $exists = \App\Models\AffiliateClick::where('affiliate_id', $affiliate->id)
            ->where('ip_address', $ip)
            ->where('created_at', '>=', now()->subHour())
            ->exists();

        if (!$exists) {
            // Attempt to resolve product_id if on items page
            $productId = null;
            if ($request->routeIs('products.show')) {
                $product = $request->route('product');
                if ($product instanceof \App\Models\Product) {
                    $productId = $product->id;
                } elseif (is_numeric($product)) {
                    $productId = $product;
                }
            }

            \App\Models\AffiliateClick::create([
                'affiliate_id' => $affiliate->id,
                'product_id' => $productId,
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
                'referenced_url' => $request->fullUrl(),
            ]);
        }
    }
}
