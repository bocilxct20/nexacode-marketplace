<?php

namespace App\Enums;

enum BuyerReportStatus: string
{
    case PENDING = 'pending';
    case UNDER_REVIEW = 'under_review';
    case RESOLVED = 'resolved';
    case DISMISSED = 'dismissed';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Review',
            self::UNDER_REVIEW => 'Sedang Ditinjau',
            self::RESOLVED => 'Selesai',
            self::DISMISSED => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'amber',
            self::UNDER_REVIEW => 'blue',
            self::RESOLVED => 'lime',
            self::DISMISSED => 'zinc',
        };
    }
}
