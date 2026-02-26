<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SecurityService
{
    /**
     * Verify the device for a user login.
     * Returns true if device is known/trusted, false if suspicious.
     */
    public function verifyDevice(User $user, string $deviceId, array $meta = []): bool
    {
        $device = UserDevice::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->first();

        if ($device) {
            $device->update([
                'last_active_at' => now(),
                'ip_address' => request()->ip(),
            ]);
            return true;
        }

        // New device detected
        $this->handleUnrecognizedDevice($user, $deviceId, $meta);
        return false;
    }

    /**
     * Register a new device for the user.
     */
    public function registerDevice(User $user, string $deviceId, array $meta = [])
    {
        return UserDevice::create([
            'user_id' => $user->id,
            'device_id' => $deviceId,
            'device_name' => $meta['device_name'] ?? 'Unknown Device',
            'browser' => $meta['browser'] ?? request()->userAgent(),
            'platform' => $meta['platform'] ?? null,
            'ip_address' => request()->ip(),
            'is_trusted' => $user->devices()->count() === 0, // Trust first device automatically
            'last_active_at' => now(),
        ]);
    }

    /**
     * Handle unrecognized device login.
     */
    protected function handleUnrecognizedDevice(User $user, string $deviceId, array $meta)
    {
        Log::warning("Suspicious login detected for user {$user->email} from unrecognized device: {$deviceId}");

        // 1. Register but untrusted
        $this->registerDevice($user, $deviceId, $meta);

        // 2. Notify user via email
        // Mail::to($user->email)->send(new \App\Mail\SuspiciousLoginAlert($user, $meta));
        
        // 3. Create a notification
        $user->notify(new \App\Notifications\SuspiciousLoginDetected([
            'ip' => request()->ip(),
            'device' => $meta['device_name'] ?? 'Unknown',
            'time' => now()->toDateTimeString(),
        ]));
    }
}
