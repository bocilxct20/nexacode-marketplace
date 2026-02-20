<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'type',
        'name',
        'account_number',
        'account_name',
        'qris_static',
        'logo',
        'instructions',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'instructions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only active payment methods
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Scope to filter by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get orders using this payment method
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if this is a QRIS payment method
     */
    public function isQris(): bool
    {
        return $this->type === 'qris';
    }

    /**
     * Check if this is a bank transfer payment method
     */
    public function isBankTransfer(): bool
    {
        return $this->type === 'bank_transfer';
    }
}
