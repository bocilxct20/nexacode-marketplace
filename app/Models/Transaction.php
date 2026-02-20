<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_type',
        'gross_amount',
        'status',
        'transaction_time',
        'settlement_time',
        'fraud_status',
        'bank',
        'va_number',
        'bill_key',
        'biller_code',
        'metadata',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'transaction_time' => 'datetime',
        'settlement_time' => 'datetime',
        'metadata' => 'array',
        'status' => \App\Enums\TransactionStatus::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isPending(): bool
    {
        return $this->status === \App\Enums\TransactionStatus::PENDING;
    }

    public function isSettled(): bool
    {
        return $this->status === \App\Enums\TransactionStatus::SETTLEMENT;
    }

    public function isFailed(): bool
    {
        return in_array($this->status, [
            \App\Enums\TransactionStatus::EXPIRE,
            \App\Enums\TransactionStatus::CANCEL,
            \App\Enums\TransactionStatus::DENY,
        ]);
    }

    public function getPaymentMethodName(): string
    {
        return match($this->payment_type) {
            'credit_card' => 'Credit Card',
            'bank_transfer' => 'Bank Transfer',
            'echannel' => 'Mandiri Bill',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'qris' => 'QRIS',
            'cstore' => 'Convenience Store',
            default => ucfirst(str_replace('_', ' ', $this->payment_type ?? 'Unknown')),
        };
    }
}
