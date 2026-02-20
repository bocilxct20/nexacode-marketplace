<?php

namespace App\Services;

use App\Models\EmailPreference;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    /**
     * Send email if user has enabled that notification type
     */
    public function sendIfEnabled(User $user, string $notificationType, $mailable): bool
    {
        try {
            $preferences = EmailPreference::forUser($user->id);
            
            if (!$preferences->wantsEmail($notificationType)) {
                Log::info("Email skipped - user disabled {$notificationType}", ['user_id' => $user->id]);
                return false;
            }

            Mail::to($user->email)->queue($mailable);
            Log::info("Email queued: {$notificationType}", ['user_id' => $user->id]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send {$notificationType} email", [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send email to multiple users
     */
    public function sendToMany(array $users, string $notificationType, $mailable): int
    {
        $sent = 0;
        
        foreach ($users as $user) {
            if ($this->sendIfEnabled($user, $notificationType, $mailable)) {
                $sent++;
            }
        }
        
        return $sent;
    }

    /**
     * Send email without checking preferences (for critical emails)
     */
    public function sendForced(User $user, $mailable): bool
    {
        try {
            Mail::to($user->email)->queue($mailable);
            Log::info("Forced email queued", ['user_id' => $user->id]);
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send forced email", [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
