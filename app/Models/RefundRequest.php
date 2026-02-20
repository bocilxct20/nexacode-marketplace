<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'reason',
        'status',
        'admin_response',
        'admin_notes',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'status' => \App\Enums\RefundStatus::class,
        'processed_at' => 'datetime',
    ];

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
