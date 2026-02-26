<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class GamificationService
{
    /**
     * XP Values for various ecosystem actions.
     */
    const ACTIONS = [
        'help_article_feedback' => 5,
        'product_purchase' => 100,
        'product_review' => 40,
    ];

    /**
     * Process daily login streak and reward XP.
     */
    public function processLoginStreak(User $user)
    {
        $lastUpdate = $user->streak_last_updated_at;
        $now = now();

        if (!$lastUpdate) {
            // First time login for streak
            $user->update([
                'current_login_streak' => 1,
                'max_login_streak' => 1,
                'streak_last_updated_at' => $now,
            ]);
            $this->awardXP($user, 50, 'First time streak reward');
            return;
        }

        $diffInDays = (int) $now->startOfDay()->diffInDays($lastUpdate->startOfDay());

        if ($diffInDays === 1) {
            // Consecutive day login
            $newStreak = $user->current_login_streak + 1;
            $user->update([
                'current_login_streak' => $newStreak,
                'max_login_streak' => max($newStreak, $user->max_login_streak),
                'streak_last_updated_at' => $now,
            ]);

            // Bonus XP for streaks
            $extraXP = min($newStreak * 10, 200); // Caps at 200 bonus
            $this->awardXP($user, 50 + $extraXP, "{$newStreak} day login streak");
            
        } elseif ($diffInDays > 1) {
            // Streak broken
            $user->update([
                'current_login_streak' => 1,
                'streak_last_updated_at' => $now,
            ]);
            $this->awardXP($user, 50, 'Streak restarted');
        }
        // If diffInDays === 0, already logged in today, do nothing
    }

    /**
     * Award XP based on a specific ecosystem action.
     */
    public function awardActionXP(User $user, string $action)
    {
        if (isset(self::ACTIONS[$action])) {
            $this->awardXP($user, self::ACTIONS[$action], "Action: " . str_replace('_', ' ', $action));
        }
    }

    /**
     * Award XP to a user and handle level up.
     */
    public function awardXP(User $user, int $amount, string $reason = '')
    {
        $oldLevel = $user->level ?: 1;
        $newXP = ($user->xp ?: 0) + $amount;
        
        // Simple level logic: Level = floor(sqrt(XP / 100)) + 1
        $newLevel = (int) floor(sqrt($newXP / 100)) + 1;

        $user->update([
            'xp' => $newXP,
            'level' => $newLevel,
        ]);

        Log::info("User {$user->id} awarded {$amount} XP for: {$reason}. Level: {$newLevel}");

        if ($newLevel > $oldLevel) {
            $user->notify(new \App\Notifications\LevelUpAchieved($newLevel));
        }
    }
}
