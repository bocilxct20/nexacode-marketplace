<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductView extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'ip_address',
        'user_agent',
        'referrer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Track a product view
     */
    public static function track(int $productId, ?int $userId = null): void
    {
        static::create([
            'product_id' => $productId,
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->header('referer'),
        ]);
    }

    /**
     * Get unique views count for a product
     */
    public static function uniqueViewsCount(int $productId, int $days = 30): int
    {
        return static::where('product_id', $productId)
            ->where('created_at', '>=', now()->subDays($days))
            ->distinct('ip_address')
            ->count('ip_address');
    }

    /**
     * Get total views count for a product
     */
    public static function totalViewsCount(int $productId, int $days = 30): int
    {
        return static::where('product_id', $productId)
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
    }
}
