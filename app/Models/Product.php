<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'author_id',
        'name',
        'slug',
        'description',
        'thumbnail',
        'screenshots',
        'video_url',
        'demo_url',
        'price',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($product) {
            // Detect Price Drop
            if ($product->wasChanged('price') && $product->price < $product->getOriginal('price')) {
                $oldPrice = $product->getOriginal('price');
                $newPrice = $product->price;

                // Notify Wishlist Users
                $wishlistUsers = \App\Models\User::whereHas('wishlists', function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })->get();

                foreach ($wishlistUsers as $user) {
                    $user->notify(new \App\Notifications\PriceDropNotification($product, $oldPrice, $newPrice));
                }
            }
        });
    }

    /**
     * Get the product's conversion rate.
     */
    public function getConversionRateAttribute()
    {
        if ($this->views_count <= 0) return 0;
        return round(($this->sales_count / $this->views_count) * 100, 2);
    }

    protected $casts = [
        'screenshots' => 'array',
        'price' => 'decimal:2',
        'status' => \App\Enums\ProductStatus::class,
    ];

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }
    
    public function isApproved(): bool
    {
        return $this->status === \App\Enums\ProductStatus::APPROVED;
    }

    /**
     * Get the current active flash sale (cached per request)
     */
    protected static ?FlashSale $activeFlashSale = null;

    public static function getActiveFlashSale()
    {
        if (static::$activeFlashSale === null) {
            static::$activeFlashSale = FlashSale::active();
        }
        return static::$activeFlashSale;
    }

    /**
     * Get the discounted price during an active flash sale.
     */
    public function getDiscountedPriceAttribute()
    {
        $sale = static::getActiveFlashSale();
        
        if ($sale && $sale->discount_percentage > 0) {
            return $this->price * (1 - ($sale->discount_percentage / 100));
        }

        return $this->price;
    }

    /**
     * Check if the product is currently on flash sale.
     */
    public function getIsOnSaleAttribute(): bool
    {
        $sale = static::getActiveFlashSale();
        return $sale && $sale->discount_percentage > 0;
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function versions()
    {
        return $this->hasMany(ProductVersion::class);
    }

    public function tags()
    {
        return $this->belongsToMany(ProductTag::class, 'product_tag');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function bundles()
    {
        return $this->belongsToMany(ProductBundle::class, 'bundle_products', 'product_id', 'bundle_id')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope for eager loading common relationships
     */
    public function scopeWithCommonRelations($query)
    {
        return $query->with(['author', 'tags', 'versions']);
    }

    /**
     * Scope for approved products
     */
    public function scopeApproved($query)
    {
        return $query->where('status', \App\Enums\ProductStatus::APPROVED);
    }

    /**
     * Scope for trending products (latest approved)
     */
    public function scopeTrending($query, $limit = 6)
    {
        return $query->approved()
            ->with(['author', 'tags'])
            ->orderBy('is_elite_marketed', 'desc')
            ->latest()
            ->take($limit);
    }

    /**
     * Collections that include this product.
     */
    public function collections()
    {
        return $this->belongsToMany(Collection::class)->withTimestamps();
    }

    /**
     * Get the dynamic thumbnail URL (external or local storage).
     */
    public function getThumbnailUrlAttribute()
    {
        if (!$this->thumbnail) {
            return null;
        }

        $thumbnail = trim($this->thumbnail);

        if (str_starts_with($thumbnail, 'http') || str_starts_with($thumbnail, 'https') || str_starts_with($thumbnail, '//')) {
            return $thumbnail;
        }

        return \Illuminate\Support\Facades\Storage::url($thumbnail);
    }

    public function getScreenshotsUrlsAttribute(): array
    {
        $screenshots = $this->screenshots ?? [];
        if (empty($screenshots)) {
            return [$this->thumbnail_url];
        }

        return array_map(function ($path) {
            if (str_starts_with($path, 'http') || str_starts_with($path, 'https') || str_starts_with($path, '//')) {
                return $path;
            }
            return \Illuminate\Support\Facades\Storage::url($path);
        }, $screenshots);
    }

    /**
     * Get social sharing URLs.
     */
    public function getShareUrlsAttribute()
    {
        $url = route('products.show', $this->slug);
        $text = urlencode("Check out " . $this->name . " on NEXACODE Marketplace!");

        return [
            'twitter' => "https://twitter.com/intent/tweet?url={$url}&text={$text}",
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$url}",
            'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url={$url}",
        ];
    }

    /**
     * Scope for featured products
     */
    public function scopeFeatured($query)
    {
        return $query->approved()->where('is_featured', true);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function analytics()
    {
        return $this->hasMany(ProductAnalytics::class);
    }

    /**
     * Increment the views count and track daily analytics.
     */
    public function trackView()
    {
        $this->increment('views_count');

        ProductAnalytics::updateOrCreate(
            ['product_id' => $this->id, 'date' => now()->toDateString()],
            ['views_count' => \Illuminate\Support\Facades\DB::raw('views_count + 1')]
        );
    }

    /**
     * Track daily sales and revenue.
     */
    public function trackSale($amount)
    {
        ProductAnalytics::updateOrCreate(
            ['product_id' => $this->id, 'date' => now()->toDateString()],
            [
                'sales_count' => \Illuminate\Support\Facades\DB::raw('sales_count + 1'),
                'revenue' => \Illuminate\Support\Facades\DB::raw("revenue + $amount")
            ]
        );
    }
}
