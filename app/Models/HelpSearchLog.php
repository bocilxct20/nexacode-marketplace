<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpSearchLog extends Model
{
    protected $fillable = [
        'query',
        'results_count',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
