<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PendingEmailChange;
use App\Models\SecurityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailChangeController extends Controller
{
    /**
     * Konfirmasi perubahan email via token dari link email.
     * Route: GET /email/change/confirm/{token}
     */
    public function confirm(Request $request, string $token)
    {
        $pending = PendingEmailChange::findValid($token);

        if (!$pending) {
            return redirect()->route('profile')
                ->withErrors(['email' => 'Link konfirmasi tidak valid atau sudah kadaluarsa.']);
        }

        $user = $pending->user;

        // Simpan email lama untuk logging
        $oldEmail = $user->email;
        $newEmail = $pending->new_email;

        // Terapkan email baru
        $user->update([
            'email'          => $newEmail,
            'pending_email'  => null,
            'email_verified_at' => now(), // Email baru sudah terverifikasi
        ]);

        // Hapus pending request
        $pending->delete();

        // Log security event
        SecurityLog::log('email_changed', $user->id, [
            'old_email' => $oldEmail,
            'new_email' => $newEmail,
        ]);

        // Jika user sedang login, refresh session user
        if (Auth::id() === $user->id) {
            Auth::setUser($user->fresh());
        }

        return redirect()->route('profile')
            ->with('success', "Email berhasil diubah ke {$newEmail}.");
    }
}
