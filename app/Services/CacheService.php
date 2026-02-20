<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheService
{
    /**
     * Remember data with callback
     */
    public function remember(string $key, $ttl, \Closure $callback)
    {
        if (!config('performance.cache.enabled')) {
            return $callback();
        }

        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Remember data forever
     */
    public function rememberForever(string $key, \Closure $callback)
    {
        if (!config('performance.cache.enabled')) {
            return $callback();
        }

        return Cache::rememberForever($key, $callback);
    }

    /**
     * Forget cache key
     */
    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    /**
     * Flush cache by tag
     */
    public function flush(?string $tag = null): bool
    {
        if ($tag) {
            return Cache::tags($tag)->flush();
        }

        return Cache::flush();
    }

    /**
     * Get cache with tags
     */
    public function tags(array $tags)
    {
        return Cache::tags($tags);
    }

    /**
     * Warm critical caches
     */
    public function warmCache(): void
    {
        $this->warmProductsCache();
        $this->warmCategoriesCache();
        $this->warmTrendingProductsCache();
    }

    /**
     * Warm products cache
     */
    protected function warmProductsCache(): void
    {
        $this->remember('products.approved', config('performance.cache.query_ttl'), function () {
            return \App\Models\Product::with(['author', 'category', 'tags'])
                ->where('status', 'approved')
                ->latest()
                ->get();
        });
    }

    /**
     * Warm categories cache
     */
    protected function warmCategoriesCache(): void
    {
        $this->remember('categories.all', config('performance.cache.default_ttl'), function () {
            return \App\Models\Category::withCount('products')->get();
        });
    }

    /**
     * Warm trending products cache
     */
    protected function warmTrendingProductsCache(): void
    {
        $this->remember('products.trending', config('performance.cache.query_ttl'), function () {
            return \App\Models\Product::with(['author', 'category'])
                ->where('status', 'approved')
                ->latest()
                ->take(6)
                ->get();
        });
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        // This is a simplified version - actual implementation would depend on Redis
        return [
            'enabled' => config('performance.cache.enabled'),
            'driver' => config('cache.default'),
            'default_ttl' => config('performance.cache.default_ttl'),
        ];
    }

    /**
     * Clear product caches
     */
    public function clearProductCaches(): void
    {
        $this->forget('products.approved');
        $this->forget('products.trending');
        $this->flush('products');
    }

    /**
     * Clear category caches
     */
    public function clearCategoryCaches(): void
    {
        $this->forget('categories.all');
        $this->forget('categories.with_counts');
        $this->flush('categories');
    }

    /**
     * Clear user caches
     */
    public function clearUserCaches(int $userId): void
    {
        $this->forget("user.{$userId}.products");
        $this->forget("user.{$userId}.stats");
        $this->flush("user.{$userId}");
    }

    /**
     * Clear analytics caches
     */
    public function clearAnalyticsCaches(int $userId): void
    {
        $this->forget("analytics.{$userId}.7days");
        $this->forget("analytics.{$userId}.30days");
        $this->forget("analytics.{$userId}.90days");
        $this->forget("analytics.{$userId}.1year");
    }
}
