<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVersion extends Model
{
    protected $fillable = [
        'product_id',
        'version_number',
        'changelog',
        'file_path',
        'is_active',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($version) {
            if ($version->is_active) {
                $product = $version->product;
                if ($product) {
                    // Optimized: Find all unique buyer IDs in one query
                    $buyerIds = \App\Models\Order::where('status', \App\Models\Order::STATUS_COMPLETED)
                        ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
                        ->pluck('buyer_id')
                        ->unique();

                    foreach ($buyerIds as $buyerId) {
                        $user = \App\Models\User::find($buyerId);
                        if ($user && $user->email && $user->wantsEmail('product_updates')) {
                            try {
                                \Illuminate\Support\Facades\Mail::to($user->email)
                                    ->queue(new \App\Mail\ProductUpdateNotification($product, $version));
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error("Failed to notify buyer {$user->id} of update: " . $e->getMessage());
                            }
                        }
                    }
                }
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
