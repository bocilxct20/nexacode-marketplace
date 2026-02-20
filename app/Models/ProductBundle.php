<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ProductBundle extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'name',
        'slug',
        'description',
        'thumbnail',
        'discount_percentage',
        'discount_amount',
        'status',
        'sales_count',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'sales_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($bundle) {
            if (!$bundle->slug) {
                $bundle->slug = Str::slug($bundle->name) . '-' . Str::random(5);
            }
        });
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'bundle_products', 'bundle_id', 'product_id')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(OrderItem::class, 'bundle_id');
    }

    public function getPriceAttribute()
    {
        $totalProductsPrice = $this->products->sum('price');
        
        if ($this->discount_amount > 0) {
            return max(0, $totalProductsPrice - $this->discount_amount);
        }
        
        if ($this->discount_percentage > 0) {
            return max(0, $totalProductsPrice * (1 - ($this->discount_percentage / 100)));
        }
        
        return $totalProductsPrice;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? \Storage::url($this->thumbnail) : asset('images/placeholder-bundle.png');
    }

    public function getTotalGrossPriceAttribute()
    {
        return $this->products->sum('price');
    }
}
