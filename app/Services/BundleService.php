<?php

namespace App\Services;

use App\Models\ProductBundle;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

class BundleService
{
    /**
     * Create a new bundle
     */
    public function createBundle(array $data, User $author)
    {
        $bundle = ProductBundle::create([
            'author_id' => $author->id,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'],
            'thumbnail' => $data['thumbnail'] ?? null,
            'discount_percentage' => $data['discount_percentage'] ?? null,
            'discount_amount' => $data['discount_amount'] ?? null,
            'status' => $data['status'] ?? 'draft',
        ]);

        // Attach products
        if (isset($data['product_ids'])) {
            $this->syncProducts($bundle, $data['product_ids']);
        }

        return $bundle;
    }

    /**
     * Update bundle
     */
    public function updateBundle(ProductBundle $bundle, array $data)
    {
        $bundle->update([
            'name' => $data['name'] ?? $bundle->name,
            'slug' => isset($data['name']) ? Str::slug($data['name']) : $bundle->slug,
            'description' => $data['description'] ?? $bundle->description,
            'thumbnail' => $data['thumbnail'] ?? $bundle->thumbnail,
            'discount_percentage' => $data['discount_percentage'] ?? $bundle->discount_percentage,
            'discount_amount' => $data['discount_amount'] ?? $bundle->discount_amount,
            'status' => $data['status'] ?? $bundle->status,
        ]);

        // Update products if provided
        if (isset($data['product_ids'])) {
            $this->syncProducts($bundle, $data['product_ids']);
        }

        return $bundle->fresh();
    }

    /**
     * Delete bundle
     */
    public function deleteBundle(ProductBundle $bundle)
    {
        $bundle->products()->detach();
        $bundle->delete();

        return true;
    }

    /**
     * Sync products with bundle
     */
    public function syncProducts(ProductBundle $bundle, array $productIds)
    {
        $syncData = [];
        foreach ($productIds as $index => $productId) {
            $syncData[$productId] = ['sort_order' => $index];
        }

        $bundle->products()->sync($syncData);

        return $bundle;
    }

    /**
     * Add product to bundle
     */
    public function addProduct(ProductBundle $bundle, Product $product)
    {
        if (!$bundle->products->contains($product->id)) {
            $sortOrder = $bundle->products()->count();
            $bundle->products()->attach($product->id, ['sort_order' => $sortOrder]);
        }

        return $bundle->fresh();
    }

    /**
     * Remove product from bundle
     */
    public function removeProduct(ProductBundle $bundle, Product $product)
    {
        $bundle->products()->detach($product->id);

        return $bundle->fresh();
    }

    /**
     * Calculate bundle price
     */
    public function calculateBundlePrice(ProductBundle $bundle)
    {
        return $bundle->calculatePrice();
    }

    /**
     * Calculate savings
     */
    public function calculateSavings(ProductBundle $bundle)
    {
        return $bundle->calculateSavings();
    }

    /**
     * Purchase bundle
     */
    public function purchaseBundle(ProductBundle $bundle, $order, User $user)
    {
        $bundle->incrementSales();

        return true;
    }
}
