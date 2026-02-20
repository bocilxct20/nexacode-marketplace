<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateClick extends Model
{
    protected $fillable = [
        'affiliate_id',
        'product_id',
        'ip_address',
        'user_agent',
        'referenced_url',
    ];

    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
