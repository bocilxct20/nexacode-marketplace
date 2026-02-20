<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'subject',
        'status',
        'priority',
    ];

    protected $casts = [
        'status' => \App\Enums\SupportStatus::class,
    ];

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public function isClosed(): bool
    {
        return $this->status === \App\Enums\SupportStatus::CLOSED;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(SupportReply::class);
    }
}
