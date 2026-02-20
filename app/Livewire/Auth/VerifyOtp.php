<?php

namespace App\Livewire\Auth;

use App\Mail\OtpVerification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Flux;

class VerifyOtp extends Component
{
    public string $code = '';

    public function mount()
    {
        if (!Auth::check() || Auth::user()->email_verified_at) {
            return redirect()->route('dashboard');
        }

        if (Auth::user()->otp_expires_at && now()->gt(Auth::user()->otp_expires_at)) {
            session()->flash('error', 'Your verification code has expired. Please request a new one.');
        }
    }

    public function verify()
    {
        $this->validate([
            'code' => 'required|numeric|digits:6',
        ]);

        $user = Auth::user();

        // ── Rate limit pada verify: maks 5 percobaan per 10 menit per user ──
        $throttleKey = 'otp-verify:' . $user->id;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('code', "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.");
            return;
        }

        if ($user->otp_code === $this->code && now()->lt($user->otp_expires_at)) {
            // Verifikasi berhasil — hapus OTP segera (one-time use)
            $user->update([
                'email_verified_at' => now(),
                'otp_code'          => null,
                'otp_expires_at'    => null,
            ]);

            // Bersihkan rate limiter karena sudah berhasil
            RateLimiter::clear($throttleKey);

            // Send Welcome Email
            Mail::to($user->email)->queue(new \App\Mail\WelcomeEmail($user));

            session()->flash('just_verified', true);

            return redirect()->route('onboarding');
        }

        // Percobaan salah — catat ke rate limiter (decay 10 menit)
        RateLimiter::hit($throttleKey, 600);

        $remaining = 5 - RateLimiter::attempts($throttleKey);
        $this->addError('code', "Kode tidak valid atau sudah kadaluarsa." . ($remaining > 0 ? " Sisa percobaan: {$remaining}." : ''));
    }

    public function resend()
    {
        $user = Auth::user();

        // Cek apakah OTP saat ini masih aktif
        if ($user->otp_expires_at && now()->lt($user->otp_expires_at)) {
            $minutes = now()->diffInMinutes($user->otp_expires_at);
            $seconds = now()->diffInSeconds($user->otp_expires_at) % 60;

            Flux::toast(
                variant: 'danger',
                heading: 'Code Still Active',
                text: "Please wait {$minutes}m {$seconds}s before requesting a new code.",
            );
            return;
        }

        // Rate limit: maks 3x resend per jam per user
        $resendKey = 'resend-otp:' . $user->id;

        if (RateLimiter::tooManyAttempts($resendKey, 3)) {
            $seconds = RateLimiter::availableIn($resendKey);
            session()->flash('error', 'Too many attempts. Please try again in ' . $seconds . ' seconds.');
            return;
        }

        RateLimiter::hit($resendKey, 3600);

        // FIX: random_int() untuk OTP yang kriptografis kuat (bukan rand())
        $otp = (string) random_int(100000, 999999);

        $user->update([
            'otp_code'       => $otp,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($user->email)->send(new OtpVerification($otp));

        // Reset rate limiter verify karena kode baru digenerate
        RateLimiter::clear('otp-verify:' . $user->id);

        $this->code = '';

        Flux::toast(
            variant: 'success',
            heading: 'Code Sent',
            text: 'A new verification code has been sent to your email.',
        );
    }

    #[Layout('layouts.auth')]
    public function render()
    {
        return view('livewire.auth.verify-otp');
    }
}
