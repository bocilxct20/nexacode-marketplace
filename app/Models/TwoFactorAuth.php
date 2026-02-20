<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class TwoFactorAuth extends Model
{
    protected $table = 'two_factor_auth';

    protected $fillable = [
        'user_id',
        'secret',
        'backup_codes',
        'recovery_email',
        'enabled',
        'confirmed_at',
    ];

    protected $casts = [
        'backup_codes' => 'array',
        'enabled' => 'boolean',
        'confirmed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get decrypted secret
     */
    public function getDecryptedSecretAttribute(): ?string
    {
        return $this->secret ? Crypt::decryptString($this->secret) : null;
    }

    /**
     * Set encrypted secret
     */
    public function setSecretAttribute($value): void
    {
        $this->attributes['secret'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Get decrypted backup codes
     */
    public function getDecryptedBackupCodesAttribute(): ?array
    {
        if (!$this->backup_codes) {
            return null;
        }

        return array_map(function ($code) {
            return Crypt::decryptString($code);
        }, $this->backup_codes);
    }

    /**
     * Set encrypted backup codes
     */
    public function setBackupCodesAttribute($value): void
    {
        if (!$value) {
            $this->attributes['backup_codes'] = null;
            return;
        }

        $encrypted = array_map(function ($code) {
            return Crypt::encryptString($code);
        }, $value);

        $this->attributes['backup_codes'] = json_encode($encrypted);
    }

    /**
     * Verify and consume a backup code
     */
    public function consumeBackupCode(string $code): bool
    {
        $decryptedCodes = $this->decrypted_backup_codes;

        if (!$decryptedCodes) {
            return false;
        }

        $index = array_search($code, $decryptedCodes);

        if ($index === false) {
            return false;
        }

        // Remove used code
        unset($decryptedCodes[$index]);
        $this->backup_codes = array_values($decryptedCodes);
        $this->save();

        return true;
    }

    /**
     * Check if backup codes are running low
     */
    public function hasLowBackupCodes(): bool
    {
        $codes = $this->decrypted_backup_codes;
        return $codes && count($codes) <= 2;
    }
}
