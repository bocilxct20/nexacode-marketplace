<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Role;
use App\Mail\OtpVerification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Flux;

class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $website_url = '';   // Honeypot

    /** What the user types in (6-char captcha text) */
    public string $captcha_input = '';

    /**
     * Per-instance UUID used as session key: captcha_{captchaKey}
     *
     * Each Livewire component instance gets its own UUID on mount.
     * This prevents multi-tab race conditions where different tabs
     * would overwrite each other's session captcha token.
     *
     * Flow:
     *   mount()          → generate UUID → stored as Livewire state (encrypted)
     *   captcha image    → GET /captcha/image?key={captchaKey} → session["captcha_{key}"]
     *   verifyCaptcha()  → checks session["captcha_{key}"] → deletes it (one-time)
     *   refreshCaptcha() → generate NEW UUID → image reloads with new key
     */
    public string $captchaKey = '';

    public function mount(): void
    {
        $this->captchaKey = (string) Str::uuid();
    }

    // ─── Captcha ──────────────────────────────────────────────────────────────

    /**
     * Verify the captcha using the per-instance session key.
     * Returns null on success, or an error string on failure.
     */
    private function verifyCaptcha(string $value): ?string
    {
        $sessionKey = "captcha_{$this->captchaKey}";
        $captcha    = session($sessionKey);

        // Missing or malformed session entry
        if (empty($captcha) || empty($captcha['token']) || empty($captcha['expires_at'])) {
            session()->forget($sessionKey);
            return 'Captcha tidak valid. Silakan refresh dan coba lagi.';
        }

        // Expired
        if (now()->gt($captcha['expires_at'])) {
            session()->forget($sessionKey);
            return 'Captcha kadaluarsa. Silakan refresh dan coba lagi.';
        }

        // Constant-time comparison (timing-attack safe)
        $expected = hash_hmac('sha256', strtoupper(trim($value)), config('app.key'));
        if (!hash_equals($expected, $captcha['token'])) {
            session()->forget($sessionKey); // One-time use — invalidate immediately
            return 'Jawaban captcha salah. Silakan refresh dan coba lagi.';
        }

        session()->forget($sessionKey); // Consumed ✓
        return null;
    }

    /**
     * Generate a new UUID key and dispatch client event to reload the image.
     * Called automatically when captcha fails, or manually via the refresh button.
     */
    public function refreshCaptcha(): void
    {
        $this->captchaKey   = (string) Str::uuid();
        $this->captcha_input = '';
        $this->dispatch('captcha-refresh', key: $this->captchaKey);
    }

    // ─── Register ─────────────────────────────────────────────────────────────

    public function register(): mixed
    {
        // Honeypot – if filled, silently abort (don't reveal bot detection)
        if (!empty($this->website_url)) {
            return null;
        }

        // Validate all regular fields
        $this->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
            'captcha_input' => ['required', 'string', 'size:6'],
        ]);

        // Validate captcha separately for clear, targeted error messaging
        $captchaError = $this->verifyCaptcha($this->captcha_input);
        if ($captchaError !== null) {
            $this->refreshCaptcha(); // Force new image
            $this->addError('captcha_input', $captchaError);
            return null;
        }

        // ── All checks passed — create the user ──────────────────────────────
        $otp = (string) random_int(100000, 999999);

        $user = User::create([
            'name'           => $this->name,
            'email'          => $this->email,
            'password'       => Hash::make($this->password),
            'otp_code'       => $otp,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        $buyerRole = Role::where('slug', 'buyer')->first();
        if ($buyerRole) {
            $user->roles()->attach($buyerRole);
        }

        Mail::to($user->email)->send(new OtpVerification($otp));

        Auth::login($user);

        Flux::toast(
            variant: 'success',
            heading: 'Account Created',
            text: 'Welcome to NEXACODE! Please verify your email.',
        );

        session()->flash('registered', true);

        return redirect()->route('verification.notice');
    }

    #[Layout('layouts.auth')]
    public function render()
    {
        return view('livewire.auth.register');
    }
}
