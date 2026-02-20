<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailPreference extends Model
{
    protected $fillable = [
        'user_id',
        'order_confirmations',
        'sale_notifications',
        'product_updates',
        'review_notifications',
        'withdrawal_notifications',
        'marketing_emails',
        'newsletter',
        'admin_notifications',
    ];

    protected $casts = [
        'order_confirmations' => 'boolean',
        'sale_notifications' => 'boolean',
        'product_updates' => 'boolean',
        'review_notifications' => 'boolean',
        'withdrawal_notifications' => 'boolean',
        'marketing_emails' => 'boolean',
        'newsletter' => 'boolean',
        'admin_notifications' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user wants to receive a specific type of email
     */
    public function wantsEmail(string $type): bool
    {
        return $this->{$type} ?? false;
    }

    /**
     * Get or create email preferences for a user
     */
    public static function forUser(int $userId): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            [
                'order_confirmations' => true,
                'sale_notifications' => true,
                'product_updates' => true,
                'review_notifications' => true,
                'withdrawal_notifications' => true,
                'marketing_emails' => true,
                'newsletter' => true,
                'admin_notifications' => true,
            ]
        );
    }
}
