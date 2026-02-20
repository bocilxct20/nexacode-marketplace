<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

/**
 * RegisterController — DEPRECATED
 *
 * Registration is fully handled by the Livewire component:
 *   App\Livewire\Auth\Register
 *
 * Route: GET /register → App\Livewire\Auth\Register (see routes/web.php)
 *
 * This controller is kept as a stub to avoid breaking existing imports.
 * DO NOT add registration logic here — the Livewire component contains
 * the secured captcha (image-based, HMAC-signed, one-time-use) and
 * all validation rules.
 */
class RegisterController extends Controller
{
    // Intentionally empty — see App\Livewire\Auth\Register
}
