<?php

namespace App\Enums;

enum ProductStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case DRAFT = 'draft';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending Review',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::DRAFT => 'Draft',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'amber',
            self::APPROVED => 'emerald',
            self::REJECTED => 'red',
            self::DRAFT => 'zinc',
        };
    }
}
