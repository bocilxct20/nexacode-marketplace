<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PlatformSetting;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isMaintenance = (bool) PlatformSetting::get('maintenance_mode', false);

        if ($isMaintenance) {
            // Bypass for admins and specific routes
            if ($request->is('admin*') || ($request->user() && $request->user()->isAdmin())) {
                return $next($request);
            }

            if ($request->is('login', 'logout', 'otp*', 'verify-otp*')) {
                return $next($request);
            }

            abort(503, 'Platform is currently under maintenance. Please check back later.');
        }

        return $next($request);
    }
}
