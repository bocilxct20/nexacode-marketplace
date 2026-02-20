<?php

namespace App\Enums;

enum CouponStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::EXPIRED => 'Expired',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'emerald',
            self::INACTIVE => 'zinc',
            self::EXPIRED => 'red',
        };
    }
}
