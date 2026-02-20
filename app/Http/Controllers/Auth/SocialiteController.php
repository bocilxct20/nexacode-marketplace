<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    /**
     * Daftar provider yang diizinkan.
     * (Lapisan kedua — route sudah dibatasi dengan whereIn, ini defence-in-depth.)
     */
    private const ALLOWED_PROVIDERS = ['google', 'github'];

    public function redirect($provider)
    {
        // Defence-in-depth: pastikan provider valid meski route constraint dilewati
        if (!in_array($provider, self::ALLOWED_PROVIDERS, true)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        if (!in_array($provider, self::ALLOWED_PROVIDERS, true)) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'social' => 'Gagal login dengan ' . ucfirst($provider) . '. Silakan coba lagi.',
            ]);
        }

        $idColumn = $provider . '_id';
        $isNewUser = false;

        // Cari user berdasarkan social ID atau email
        $user = User::where($idColumn, $socialUser->getId())
            ->orWhere('email', $socialUser->getEmail())
            ->first();

        if (!$user) {
            // ── User baru via social ─────────────────────────────────────────
            $isNewUser = true;

            $user = User::create([
                'name'             => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                'email'            => $socialUser->getEmail(),
                'password'         => bcrypt(Str::random(32)),
                $idColumn          => $socialUser->getId(),
                'avatar'           => $socialUser->getAvatar(), // URL avatar dari provider
                'email_verified_at'=> now(), // Social emails sudah terverifikasi
            ]);

            // Assign role buyer secara default
            $buyerRole = Role::where('slug', 'buyer')->first();
            if ($buyerRole) {
                $user->roles()->attach($buyerRole);
            }
        } else {
            // ── User sudah ada ───────────────────────────────────────────────
            // Update social ID jika belum tersimpan (misalnya: daftar via email, lalu login via Google)
            if (!$user->$idColumn) {
                $user->update([$idColumn => $socialUser->getId()]);
            }
        }

        // Catat aktivitas login
        $user->update([
            'last_login_at'    => now(),
            'last_login_ip'    => request()->ip(),
            'last_login_device'=> request()->userAgent(),
        ]);

        // Catat ke Security Log
        \App\Models\SecurityLog::log('social_login_success', $user->id, [
            'provider' => $provider,
            'is_new'   => $isNewUser,
        ]);

        Auth::login($user);

        // ── Redirect berdasarkan kondisi ────────────────────────────────────
        // User baru ATAU user lama yang belum punya username → onboarding
        // User lama yang sudah punya username → dashboard (role-based)
        if ($isNewUser || !$user->username) {
            return redirect()->route('onboarding');
        }

        // Role-based redirect untuk user yang sudah onboarding
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('author')) {
            return redirect()->route('author.dashboard');
        }

        return redirect()->route('home');
    }
}
