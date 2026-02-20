<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateEarning extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'product_id',
        'amount',
        'commission_rate',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
