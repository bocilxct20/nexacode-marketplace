<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case AUTHOR = 'author';
    case BUYER = 'buyer';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::AUTHOR => 'Author',
            self::BUYER => 'Buyer',
        };
    }
}
