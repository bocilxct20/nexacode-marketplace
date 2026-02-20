<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING = 'pending';
    case SETTLEMENT = 'settlement';
    case EXPIRE = 'expire';
    case CANCEL = 'cancel';
    case DENY = 'deny';
    case CHALLENGE = 'challenge';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::SETTLEMENT => 'Settled',
            self::EXPIRE => 'Expired',
            self::CANCEL => 'Cancelled',
            self::DENY => 'Denied',
            self::CHALLENGE => 'Challenged',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'amber',
            self::SETTLEMENT => 'emerald',
            self::EXPIRE => 'zinc',
            self::CANCEL => 'red',
            self::DENY => 'red',
            self::CHALLENGE => 'orange',
        };
    }
}
