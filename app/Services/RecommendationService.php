<?php

namespace App\Services;

use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Cache;

class RecommendationService
{
    /**
     * Get "Users also bought" recommendations for a product.
     */
    public function getAlsoBought(Product $product, int $limit = 4)
    {
        return Cache::remember("recommendations.also_bought.{$product->id}", 3600, function () use ($product, $limit) {
            // Find orders containing this product
            $orderIds = OrderItem::where('product_id', $product->id)
                ->pluck('order_id');

            if ($orderIds->isEmpty()) {
                return $this->getRelatedByCategory($product, $limit);
            }

            // Find other products in those same orders
            $alsoBoughtIds = OrderItem::whereIn('order_id', $orderIds)
                ->where('product_id', '!=', $product->id)
                ->select('product_id')
                ->selectRaw('COUNT(product_id) as purchase_count')
                ->groupBy('product_id')
                ->orderByDesc('purchase_count')
                ->limit($limit)
                ->pluck('product_id');

            if ($alsoBoughtIds->isEmpty()) {
                return $this->getRelatedByCategory($product, $limit);
            }

            return Product::whereIn('id', $alsoBoughtIds)
                ->approved()
                ->with(['author', 'tags'])
                ->get();
        });
    }

    /**
     * Fallback to related products by category/tags.
     */
    public function getRelatedByCategory(Product $product, int $limit = 4)
    {
        $tagIds = $product->tags->pluck('id');

        return Product::where('id', '!=', $product->id)
            ->approved()
            ->whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('product_tags.id', $tagIds);
            })
            ->with(['author', 'tags'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
