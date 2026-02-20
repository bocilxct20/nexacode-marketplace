<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAnalytics extends Model
{
    protected $fillable = [
        'product_id',
        'date',
        'views_count',
        'sales_count',
        'revenue',
    ];

    protected $casts = [
        'date' => 'date',
        'revenue' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
