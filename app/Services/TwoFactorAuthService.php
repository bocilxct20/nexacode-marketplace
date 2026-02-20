<?php

namespace App\Services;

use App\Models\User;
use App\Models\TwoFactorAuth;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;

class TwoFactorAuthService
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Generate a new TOTP secret
     */
    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Generate QR code for authenticator app
     */
    public function generateQrCode(User $user, string $secret): string
    {
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        return $writer->writeString($qrCodeUrl);
    }

    /**
     * Generate backup codes
     */
    public function generateBackupCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(4) . '-' . Str::random(4));
        }
        return $codes;
    }

    /**
     * Verify TOTP code
     */
    public function verifyCode(User $user, string $code): bool
    {
        $twoFactor = $user->twoFactorAuth;

        if (!$twoFactor || !$twoFactor->enabled) {
            return false;
        }

        $secret = $twoFactor->decrypted_secret;

        if (!$secret) {
            return false;
        }

        return $this->google2fa->verifyKey($secret, $code);
    }

    /**
     * Verify backup code
     */
    public function verifyBackupCode(User $user, string $code): bool
    {
        $twoFactor = $user->twoFactorAuth;

        if (!$twoFactor || !$twoFactor->enabled) {
            return false;
        }

        return $twoFactor->consumeBackupCode($code);
    }

    /**
     * Enable 2FA for user
     */
    public function enable(User $user, string $secret, array $backupCodes): TwoFactorAuth
    {
        $twoFactor = TwoFactorAuth::updateOrCreate(
            ['user_id' => $user->id],
            [
                'secret' => $secret,
                'backup_codes' => $backupCodes,
                'enabled' => true,
                'confirmed_at' => now(),
            ]
        );

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
        ]);

        return $twoFactor;
    }

    /**
     * Disable 2FA for user
     */
    public function disable(User $user): void
    {
        $twoFactor = $user->twoFactorAuth;

        if ($twoFactor) {
            $twoFactor->update([
                'enabled' => false,
                'confirmed_at' => null,
            ]);
        }

        $user->update([
            'two_factor_enabled' => false,
            'two_factor_confirmed_at' => null,
            'remember_2fa_token' => null,
        ]);
    }

    /**
     * Regenerate backup codes
     */
    public function regenerateBackupCodes(User $user): array
    {
        $twoFactor = $user->twoFactorAuth;

        if (!$twoFactor) {
            throw new \Exception('2FA not enabled for this user');
        }

        $backupCodes = $this->generateBackupCodes();
        $twoFactor->update(['backup_codes' => $backupCodes]);

        return $backupCodes;
    }

    /**
     * Generate remember device token
     */
    public function generateRememberToken(User $user): string
    {
        $token = Str::random(60);
        
        $user->update([
            'remember_2fa_token' => hash('sha256', $token),
        ]);

        return $token;
    }

    /**
     * Verify remember device token
     */
    public function verifyRememberToken(User $user, string $token): bool
    {
        if (!$user->remember_2fa_token) {
            return false;
        }

        return hash_equals($user->remember_2fa_token, hash('sha256', $token));
    }
}
