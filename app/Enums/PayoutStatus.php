<?php

namespace App\Enums;

enum PayoutStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::PAID => 'Paid',
            self::CANCELLED => 'Cancelled',
        };
    }
}
