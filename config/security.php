<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication
    |--------------------------------------------------------------------------
    */
    'two_factor' => [
        'enabled' => env('TWO_FACTOR_ENABLED', true),
        'force_for_admins' => true,
        'remember_device_days' => 30,
        'backup_codes_count' => 8,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'api' => [
            'per_minute' => 60,
            'per_hour' => 1000,
        ],
        'login' => [
            'max_attempts' => 5,
            'decay_minutes' => 15,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    */
    'session' => [
        'lifetime' => 120, // minutes
        'expire_on_close' => false,
        'max_concurrent_sessions' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Policy
    |--------------------------------------------------------------------------
    */
    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_special_chars' => true,
        'expire_days' => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    */
    'headers' => [
        'csp' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://app.sandbox.midtrans.com https://static.cloudflareinsights.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net; font-src 'self' data: https://fonts.bunny.net; img-src 'self' data: https:; media-src 'self' https://assets.mixkit.co; connect-src 'self' https://cdn.jsdelivr.net https://app.sandbox.midtrans.com https://static.cloudflareinsights.com ws://localhost:8081 wss://localhost:8081 wss://" . env('DOMAIN', 'nexacode.id') . ";",
        'hsts' => 'max-age=31536000; includeSubDomains',
        'x_frame_options' => 'SAMEORIGIN',
        'x_content_type_options' => 'nosniff',
    ],
];
