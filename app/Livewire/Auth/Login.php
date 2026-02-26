<?php

namespace App\Livewire\Auth;

use App\Mail\NewLoginAlert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Flux;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public string $deviceId = '';
    public array $deviceMeta = [];

    public function login()
    {
        $this->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($this->email).'|'.request()->ip());

        // ── Rate limiter (sudah ada) ─────────────────────────────────────────
        if (RateLimiter::tooManyAttempts($throttleKey, config('security.rate_limiting.login.max_attempts', 5))) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        // ── Feature #6: Cek account lockout SEBELUM Auth::attempt ──────────
        // Lookup user dulu untuk cek apakah akun dikunci
        $lockCheckUser = \App\Models\User::where('email', $this->email)->first();
        if ($lockCheckUser &&
            $lockCheckUser->account_locked_until &&
            $lockCheckUser->account_locked_until->isFuture()
        ) {
            $minutes = (int) now()->diffInMinutes($lockCheckUser->account_locked_until);
            throw ValidationException::withMessages([
                'email' => "Akun kamu dikunci karena terlalu banyak percobaan login gagal. Coba lagi dalam {$minutes} menit.",
            ]);
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();

            $user = Auth::user();

            // ── Feature #6: Reset failed counter setelah login berhasil ─────
            if ($user->failed_login_count > 0 || $user->account_locked_until) {
                $user->update([
                    'failed_login_count'   => 0,
                    'account_locked_until' => null,
                ]);
            }

            // ── Device Fingerprinting & Security Check ─────────────────
            $securityService = app(\App\Services\SecurityService::class);
            if ($this->deviceId) {
                $securityService->verifyDevice($user, $this->deviceId, $this->deviceMeta);
            }

            // ── Feature #2: Deteksi login dari IP/Device baru (Legacy fallback) ──
            $currentIp       = request()->ip();
            $currentDevice   = request()->userAgent();
            // ... (keep the rest of the existing logic for logging and redirect)
            $user->update([
                'last_login_at'     => now(),
                'last_login_ip'     => $currentIp,
                'last_login_device' => $currentDevice,
            ]);

            \App\Models\SecurityLog::log('login_success', $user->id);

            // ── Gamification: Process Login Streak ──────────────────────────
            app(\App\Services\GamificationService::class)->processLoginStreak($user);

            Flux::toast(
                variant: 'success',
                heading: 'Welcome back!',
                text   : 'You have successfully logged in.',
            );

            if (!$user->email_verified_at) {
                return redirect()->route('verification.notice');
            }

            // Role-based redirect
            if ($user->hasRole('admin')) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->hasRole('author')) {
                return redirect()->intended(route('author.dashboard'));
            }

            return redirect()->intended(route('home'));
        }

        // ── Login gagal ─────────────────────────────────────────────────────
        RateLimiter::hit($throttleKey, config('security.rate_limiting.login.decay_minutes', 15) * 60);

        // ── Feature #6: Increment failed_login_count dan lockout jika >10 ──
        $failingUser = \App\Models\User::where('email', $this->email)->first();
        if ($failingUser) {
            $failedCount = $failingUser->failed_login_count + 1;
            $updateData  = ['failed_login_count' => $failedCount];

            if ($failedCount >= 10) {
                // Kunci akun selama 1 jam
                $updateData['account_locked_until'] = now()->addHour();

                // Kirim email notifikasi lockout
                Mail::to($failingUser->email)->queue(
                    new \App\Mail\SecurityAlert(
                        user        : $failingUser,
                        title       : '⚠️ Akun Kamu Dikunci Sementara',
                        messageBody : "Akun kamu dikunci selama 1 jam karena {$failedCount} kali percobaan login gagal berturut-turut. Jika bukan kamu, segera ganti password kamu setelah kunci berakhir.",
                        actionUrl   : route('password.request'),
                        actionText  : 'Reset Password',
                    )
                );

                \App\Models\SecurityLog::log('account_locked', $failingUser->id, [
                    'failed_count' => $failedCount,
                    'locked_until' => now()->addHour()->toISOString(),
                ]);
            } else {
                \App\Models\SecurityLog::log('login_failed', $failingUser->id, [
                    'attempt' => $failedCount,
                ]);
            }

            $failingUser->update($updateData);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    #[Layout('layouts.auth')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
