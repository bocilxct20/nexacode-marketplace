<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Collection extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * The user who owns the collection.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Products in the collection.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($collection) {
            if (empty($collection->slug)) {
                $collection->slug = Str::slug($collection->name) . '-' . Str::random(5);
            }
        });
    }

    /**
     * Scope for public collections.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
