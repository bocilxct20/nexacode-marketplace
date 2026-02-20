<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'code',
        'description',
        'type',
        'value',
        'min_purchase',
        'usage_limit',
        'usage_per_user',
        'starts_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'value' => 'float',
        'min_purchase' => 'float',
        'status' => \App\Enums\CouponStatus::class,
    ];

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }
    
    public function isValid(): bool
    {
        if ($this->status !== \App\Enums\CouponStatus::ACTIVE) return false;
        
        $now = now();
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        
        if ($this->usage_limit > 0 && $this->usage_count >= $this->usage_limit) return false;
        
        return true;
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'coupon_products');
    }

    public function usage()
    {
        return $this->hasMany(CouponUsage::class);
    }
}
