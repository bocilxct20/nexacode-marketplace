<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'author_id',
        'amount',
        'status',
        'payment_method',
        'admin_note',
    ];

    protected $casts = [
        'status' => \App\Enums\PayoutStatus::class,
    ];

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            \App\Enums\PayoutStatus::PAID => 'emerald',
            \App\Enums\PayoutStatus::PENDING => 'amber',
            \App\Enums\PayoutStatus::CANCELLED => 'red',
        };
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
