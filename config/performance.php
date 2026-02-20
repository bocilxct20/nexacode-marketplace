<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Performance Cache Settings
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => env('CACHE_ENABLED', true),
        'default_ttl' => 3600, // 1 hour
        'query_ttl' => 600, // 10 minutes
        'view_ttl' => 1800, // 30 minutes
        'analytics_ttl' => 3600, // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | CDN Configuration
    |--------------------------------------------------------------------------
    */
    'cdn' => [
        'enabled' => env('CDN_ENABLED', false),
        'url' => env('CDN_URL'),
        'assets' => env('CDN_ASSETS', true),
        'images' => env('CDN_IMAGES', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Compression
    |--------------------------------------------------------------------------
    */
    'compression' => [
        'enabled' => true,
        'level' => 6, // 1-9 (higher = better compression, slower)
        'min_size' => 1024, // bytes (don't compress responses smaller than this)
        'types' => [
            'text/html',
            'text/css',
            'text/javascript',
            'application/javascript',
            'application/json',
            'application/xml',
            'text/xml',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Optimization
    |--------------------------------------------------------------------------
    */
    'images' => [
        'lazy_load' => true,
        'webp_conversion' => env('IMAGE_WEBP_CONVERSION', true),
        'quality' => env('IMAGE_QUALITY', 85),
        'max_width' => 2000,
        'max_height' => 2000,
        'responsive_sizes' => [400, 800, 1200, 1600],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Optimization
    |--------------------------------------------------------------------------
    */
    'database' => [
        'eager_load' => true,
        'chunk_size' => 1000,
        'query_timeout' => 30, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Optimization
    |--------------------------------------------------------------------------
    */
    'assets' => [
        'minify' => env('APP_ENV') === 'production',
        'combine' => env('APP_ENV') === 'production',
        'version' => true,
    ],
];
