<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    protected $fillable = [
        'title',
        'discount_percentage',
        'starts_at',
        'ends_at',
        'is_active',
        'banner_message',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the currently active flash sale.
     */
    public static function active()
    {
        return self::where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();
    }
}
