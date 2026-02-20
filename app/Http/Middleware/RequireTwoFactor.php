<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Check if user has 2FA enabled
        if (!$user->two_factor_enabled) {
            return $next($request);
        }

        // Check if already verified in this session
        if (session('2fa_verified')) {
            return $next($request);
        }

        // Check remember device token
        $rememberToken = $request->cookie('remember_2fa');
        if ($rememberToken) {
            $twoFactorService = app(\App\Services\TwoFactorAuthService::class);
            if ($twoFactorService->verifyRememberToken($user, $rememberToken)) {
                session(['2fa_verified' => true]);
                return $next($request);
            }
        }

        // Redirect to 2FA verification
        return redirect()->route('two-factor.verify');
    }
}
