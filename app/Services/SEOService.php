<?php

namespace App\Services;

use App\Models\Product;

class SEOService
{
    /**
     * Generate JSON-LD Schema for a product.
     */
    public function generateProductSchema(Product $product): string
    {
        $schema = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $product->thumbnail_url,
            'description' => strip_tags($product->description),
            'sku' => $product->slug,
            'brand' => [
                '@type' => 'Brand',
                'name' => 'NexaCode'
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => route('products.show', $product->slug),
                'priceCurrency' => 'IDR',
                'price' => $product->is_on_sale ? $product->discounted_price : $product->price,
                'availability' => 'https://schema.org/InStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'NexaCode Marketplace'
                ]
            ]
        ];

        if ($product->avg_rating > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $product->avg_rating,
                'reviewCount' => $product->reviews_count ?: 1,
                'bestRating' => '5',
                'worstRating' => '1'
            ];
        }

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
