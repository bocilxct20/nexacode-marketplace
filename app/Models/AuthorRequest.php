<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorRequest extends Model
{
    protected $fillable = ['user_id', 'portfolio_url', 'message', 'status',];

    protected $casts = [
        'status' => \App\Enums\AuthorRequestStatus::class,
    ];

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
