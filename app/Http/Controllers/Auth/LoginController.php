<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Record Login Activity
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
                'last_login_device' => $request->userAgent(),
            ]);

            // Security Check: Notify if login IP is different
            $previousIp = $user->getOriginal('last_login_ip');
            if ($previousIp && $previousIp !== $request->ip()) {
                \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\SecurityAlert(
                    $user,
                    'Login Baru dari IP Berbeda',
                    "Kami mendeteksi login baru ke akun kamu dari alamat IP: {$request->ip()}. Sebelumnya kamu login menggunakan IP: {$previousIp}.",
                    url('/security'),
                    'Cek Aktivitas'
                ));
            }

            \App\Models\SecurityLog::log('login_success', $user->id);

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


        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
