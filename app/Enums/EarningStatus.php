<?php

namespace App\Enums;

enum EarningStatus: string
{
    case PENDING = 'pending';
    case AVAILABLE = 'available';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::AVAILABLE => 'Available',
            self::CANCELLED => 'Cancelled',
        };
    }
}
