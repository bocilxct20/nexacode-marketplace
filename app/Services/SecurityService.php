<?php

namespace App\Services;

use App\Models\User;
use App\Models\SecurityLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class SecurityService
{
    /**
     * Log security activity
     */
    public function logActivity(string $action, ?User $user = null, array $data = [], ?int $status = null): void
    {
        SecurityLog::log($action, $user?->id, $data, $status);
    }

    /**
     * Check password strength
     */
    public function checkPasswordStrength(string $password): array
    {
        $errors = [];
        $config = config('security.password', []);

        $minLength = $config['min_length'] ?? 8;
        if (strlen($password) < $minLength) {
            $errors[] = "Password must be at least {$minLength} characters";
        }

        if (($config['require_uppercase'] ?? true) && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }

        if (($config['require_lowercase'] ?? true) && !preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }

        if (($config['require_numbers'] ?? true) && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }

        if (($config['require_special_chars'] ?? true) && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Detect suspicious activity
     */
    public function detectSuspiciousActivity(User $user): bool
    {
        // Check for multiple failed login attempts
        $failedAttempts = SecurityLog::where('user_id', $user->id)
            ->where('action', 'login_failed')
            ->where('created_at', '>=', now()->subHours(1))
            ->count();

        if ($failedAttempts >= 5) {
            return true;
        }

        // Check for logins from multiple IPs in short time
        $recentLogins = SecurityLog::where('user_id', $user->id)
            ->where('action', 'login_success')
            ->where('created_at', '>=', now()->subHours(1))
            ->distinct('ip_address')
            ->count('ip_address');

        if ($recentLogins >= 3) {
            return true;
        }

        return false;
    }

    /**
     * Block IP address temporarily
     */
    public function blockIpAddress(string $ip, int $minutes = 15): void
    {
        $cacheKey = "blocked_ip:{$ip}";
        Cache::put($cacheKey, true, now()->addMinutes($minutes));

        $this->logActivity('ip_blocked', null, [
            'ip' => $ip,
            'duration_minutes' => $minutes,
        ]);
    }

    /**
     * Check if IP is blocked
     */
    public function isIpBlocked(string $ip): bool
    {
        $cacheKey = "blocked_ip:{$ip}";
        return Cache::has($cacheKey);
    }

    /**
     * Get failed login attempts for IP
     */
    public function getFailedLoginAttempts(string $ip, int $minutes = 15): int
    {
        return SecurityLog::getFailedLoginAttempts($ip, $minutes);
    }

    /**
     * Check if password needs to be changed
     */
    public function needsPasswordChange(User $user): bool
    {
        if ($user->force_password_change) {
            return true;
        }

        $expireDays = config('security.password.expire_days', 90);
        
        if (!$user->password_changed_at) {
            return false;
        }

        return $user->password_changed_at->addDays($expireDays)->isPast();
    }

    /**
     * Record password change
     */
    public function recordPasswordChange(User $user): void
    {
        $user->update([
            'password_changed_at' => now(),
            'force_password_change' => false,
        ]);

        $this->logActivity('password_changed', $user);

        // Send Email Alert
        \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\SecurityAlert(
            $user,
            'Password Kamu Berhasil Diganti',
            'Password untuk akun NexaCode kamu baru saja diganti. Jika ini adalah tindakan kamu, kamu bisa mengabaikan email ini.',
            route('profile.edit'),
            'Kelola Akun'
        ));
    }

    /**
     * Check rate limit for action
     */
    public function checkRateLimit(string $key, int $maxAttempts, int $decayMinutes): bool
    {
        $cacheKey = "rate_limit:{$key}";
        $attempts = Cache::get($cacheKey, 0);

        if ($attempts >= $maxAttempts) {
            return false;
        }

        Cache::put($cacheKey, $attempts + 1, now()->addMinutes($decayMinutes));
        return true;
    }

    /**
     * Clear rate limit
     */
    public function clearRateLimit(string $key): void
    {
        $cacheKey = "rate_limit:{$key}";
        Cache::forget($cacheKey);
    }
}
