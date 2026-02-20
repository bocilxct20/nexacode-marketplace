<?php

namespace App\Enums;

enum SupportStatus: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';
    case RESOLVED = 'resolved';

    public function label(): string
    {
        return match($this) {
            self::OPEN => 'Open',
            self::CLOSED => 'Closed',
            self::RESOLVED => 'Resolved',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::OPEN => 'blue',
            self::CLOSED => 'zinc',
            self::RESOLVED => 'emerald',
        };
    }
}
