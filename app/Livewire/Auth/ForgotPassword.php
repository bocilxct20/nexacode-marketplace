<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Flux;

class ForgotPassword extends Component
{
    public string $email = '';

    public function sendResetLink()
    {
        $this->validate(['email' => 'required|email']);

        // ── Rate limit: maks 5 permintaan per IP per 15 menit ──────────────
        // Mencegah email bombing / spam ke ribuan alamat dari IP yang sama.
        $throttleKey = 'forgot-password:' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);
            $this->addError('email', "Terlalu banyak permintaan. Coba lagi dalam {$minutes} menit.");
            return;
        }

        RateLimiter::hit($throttleKey, 900); // 15 menit decay

        // Laravel password broker punya internal per-email throttling (60 detik).
        // Kita tambahkan per-IP throttling di atas untuk layer ekstra.
        $status = Password::broker()->sendResetLink(
            ['email' => $this->email]
        );

        // ── Selalu tampilkan pesan sukses (user enumeration prevention) ─────
        // Tidak boleh membedakan "email ditemukan" vs "tidak ditemukan"
        // Jika kita hanya tampilkan sukses ketika RESET_LINK_SENT,
        // penyerang bisa enumerate email mana saja yang terdaftar.
        if ($status === Password::RESET_LINK_SENT || $status === Password::INVALID_USER) {
            Flux::toast(
                variant: 'success',
                heading: 'Check Your Email',
                text: 'If an account with that email exists, we\'ve sent a password reset link.',
            );
            $this->email = '';
        } else {
            // Hanya tampilkan error teknis (throttled by broker, dll)
            $this->addError('email', __($status));
        }
    }

    #[Layout('layouts.auth')]
    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
