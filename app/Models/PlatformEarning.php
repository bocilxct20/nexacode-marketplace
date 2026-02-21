<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformEarning extends Model
{
    protected $fillable = [
        'order_id',
        'order_item_id',
        'gross_commission',
        'affiliate_payout',
        'net_profit',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
