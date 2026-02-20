<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected static function booted()
    {
        static::created(function ($review) {
            $product = $review->product;
            $author = $product ? $product->author : null;
            
            if ($author) {
                // Reward XP based on rating
                // 5 stars = 50 XP, 4 stars = 20 XP, 3 stars = 10 XP
                $xpReward = match((int) $review->rating) {
                    5 => 50,
                    4 => 20,
                    3 => 10,
                    default => 0,
                };
                
                if ($xpReward > 0) {
                    app(\App\Services\AuthorLevelService::class)->addXp($author, $xpReward);
                }
            }
        });
    }

    protected $fillable = [
        'product_id',
        'buyer_id',
        'order_id',
        'rating',
        'comment',
        'media',
        'author_reply',
        'author_replied_at',
    ];

    protected $casts = [
        'author_replied_at' => 'datetime',
        'media' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if the review is from a verified purchase.
     */
    public function isVerified()
    {
        return $this->order_id !== null && $this->order && $this->order->status === \App\Enums\OrderStatus::COMPLETED;
    }
}
