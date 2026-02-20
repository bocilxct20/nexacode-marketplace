<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PENDING_PAYMENT = 'pending_payment';
    case PENDING_VERIFICATION = 'pending_verification';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::PENDING_PAYMENT => 'Pending Payment',
            self::PENDING_VERIFICATION => 'Pending Verification',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::FAILED => 'Failed',
            self::REFUNDED => 'Refunded',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::COMPLETED => 'emerald',
            self::PENDING, self::PENDING_PAYMENT => 'amber',
            self::PENDING_VERIFICATION, self::PROCESSING => 'blue',
            self::CANCELLED, self::FAILED => 'red',
            self::REFUNDED => 'zinc',
        };
    }
}
