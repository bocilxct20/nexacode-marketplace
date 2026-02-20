<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorAuthService;
use App\Services\SecurityService;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    protected $twoFactorService;
    protected $securityService;

    public function __construct(TwoFactorAuthService $twoFactorService, SecurityService $securityService)
    {
        $this->twoFactorService = $twoFactorService;
        $this->securityService = $securityService;
    }

    /**
     * Show 2FA setup page
     */
    public function setup()
    {
        $user = auth()->user();

        if ($user->two_factor_enabled) {
            \Flux::toast(
                variant: 'info',
                heading: 'Already Active',
                text: '2FA is already enabled on your account.',
            );
            return redirect()->route($this->getProfileRoute());
        }

        $secret = $this->twoFactorService->generateSecret();
        $qrCode = $this->twoFactorService->generateQrCode($user, $secret);

        session(['2fa_secret' => $secret]);

        return view('auth.two-factor.setup', compact('secret', 'qrCode'));
    }

    /**
     * Enable 2FA
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $secret = session('2fa_secret');

        if (!$secret) {
            return back()->withErrors(['code' => 'Session expired. Please try again.']);
        }

        // Verify the code
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        if (!$google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code']);
        }

        // Generate backup codes
        $backupCodes = $this->twoFactorService->generateBackupCodes();

        // Enable 2FA
        $this->twoFactorService->enable($user, $secret, $backupCodes);

        // Log activity
        $this->securityService->logActivity('2fa_enabled', $user);

        session()->forget('2fa_secret');

        return view('auth.two-factor.backup-codes', compact('backupCodes'));
    }

    /**
     * Show 2FA verification page
     */
    public function showVerify()
    {
        return view('auth.two-factor.verify');
    }

    /**
     * Verify 2FA code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            // Catatan: 'remember' dikecualikan dari validate() karena HTML checkbox
            // mengirimkan "on" bukan "1", yang gagal aturan 'boolean'.
            // Kita pakai $request->boolean() di bawah yang bisa handle semua nilai truthy.
        ]);

        $user = auth()->user();
        $code = $request->code;

        // Try TOTP code first
        if ($this->twoFactorService->verifyCode($user, $code)) {
            session(['2fa_verified' => true]);

            // Log successful verification
            $this->securityService->logActivity('2fa_verified', $user);

            // Handle remember device — $request->boolean() handles "on", "1", true, dll
            if ($request->boolean('remember')) {
                $token = $this->twoFactorService->generateRememberToken($user);
                cookie()->queue('remember_2fa', $token, 60 * 24 * 30); // 30 hari
            }

            return redirect()->intended(route($this->getDashboardRoute()));
        }

        // Try backup code
        if ($this->twoFactorService->verifyBackupCode($user, $code)) {
            session(['2fa_verified' => true]);

            // Log backup code usage
            $this->securityService->logActivity('2fa_backup_code_used', $user);

            // Check if running low on backup codes
            if ($user->twoFactorAuth->hasLowBackupCodes()) {
                return redirect()->intended(route('dashboard'))
                    ->with('warning', 'You have 2 or fewer backup codes remaining. Please regenerate them.');
            }

            return redirect()->intended(route($this->getDashboardRoute()));
        }

        // Log failed attempt
        $this->securityService->logActivity('2fa_failed', $user);

        return back()->withErrors(['code' => 'Invalid verification code']);
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();
        $this->twoFactorService->disable($user);

        // Log activity
        $this->securityService->logActivity('2fa_disabled', $user);

        \Flux::toast(
            variant: 'success',
            heading: 'Security Updated',
            text: 'Two-factor authentication has been disabled.',
        );
        return redirect()->route($this->getProfileRoute());
    }

    /**
     * Regenerate backup codes
     */
    public function regenerateBackupCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();

        if (!$user->two_factor_enabled) {
            return back()->withErrors(['error' => '2FA is not enabled']);
        }

        $backupCodes = $this->twoFactorService->regenerateBackupCodes($user);

        // Log activity
        $this->securityService->logActivity('2fa_backup_codes_regenerated', $user);

        return view('auth.two-factor.backup-codes', compact('backupCodes'));
    }

    /**
     * Get the appropriate dashboard route based on user role.
     */
    protected function getDashboardRoute()
    {
        $user = auth()->user();
        if ($user->isAdmin()) return 'admin.dashboard';
        if ($user->isAuthor()) return 'author.dashboard';
        return 'dashboard';
    }

    /**
     * Get the appropriate profile route based on user role.
     */
    protected function getProfileRoute()
    {
        $user = auth()->user();
        if ($user->isAdmin()) return 'admin.profile';
        if ($user->isAuthor()) return 'author.profile';
        return 'profile';
    }
}
