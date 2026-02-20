<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PendingEmailChange extends Model
{
    protected $fillable = ['user_id', 'new_email', 'token', 'expires_at'];

    protected $casts = ['expires_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new pending email change token for the user.
     * Overwrites any previous pending request for this user.
     */
    public static function createFor(User $user, string $newEmail): static
    {
        static::where('user_id', $user->id)->delete();

        return static::create([
            'user_id'   => $user->id,
            'new_email' => $newEmail,
            'token'     => Str::random(64),
            'expires_at'=> now()->addHours(24),
        ]);
    }

    /**
     * Find a valid (non-expired) pending change by token.
     */
    public static function findValid(string $token): ?static
    {
        return static::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
