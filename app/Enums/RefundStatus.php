<?php

namespace App\Enums;

enum RefundStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending Review',
            self::APPROVED => 'Refunded',
            self::REJECTED => 'Declined',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'amber',
            self::APPROVED => 'emerald',
            self::REJECTED => 'red',
        };
    }
}
