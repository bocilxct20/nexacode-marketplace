<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliatePayout extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'bank_name',
        'account_number',
        'account_name',
        'admin_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
