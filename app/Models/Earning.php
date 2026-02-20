<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Earning extends Model
{
    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_AVAILABLE = 'available';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'product_id',
        'order_id',
        'author_id',
        'amount',
        'commission_amount',
        'status',
        'available_at',
    ];

    protected $casts = [
        'status' => \App\Enums\EarningStatus::class,
        'available_at' => 'datetime',
    ];

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', \App\Enums\EarningStatus::AVAILABLE);
    }

    public function scopePending($query)
    {
        return $query->where('status', \App\Enums\EarningStatus::PENDING);
    }

    // Methods
    public function markAsAvailable()
    {
        $this->update([
            'status' => \App\Enums\EarningStatus::AVAILABLE,
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => \App\Enums\EarningStatus::CANCELLED,
        ]);
    }

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
