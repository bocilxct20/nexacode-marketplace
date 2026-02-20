<?php

namespace App\Services;

use App\Models\User;

class AuthorLevelService
{
    /**
     * XP required for Level 1 is 0.
     * Formula: XP = 100 * (level^1.5)
     */
    public function getXpForLevel(int $level): int
    {
        if ($level <= 1) return 0;
        return (int) (100 * pow($level - 1, 1.5));
    }

    public function getLevelForXp(int $xp): int
    {
        $level = 1;
        while ($this->getXpForLevel($level + 1) <= $xp) {
            $level++;
        }
        return $level;
    }

    public function addXp(User $user, int $amount): void
    {
        $user->xp += $amount;
        
        $newLevel = $this->getLevelForXp($user->xp);
        
        if ($newLevel > $user->level) {
            $user->level = $newLevel;
            
            // Notify User of achievement
            $user->notify(new \App\Notifications\SystemNotification([
                'title' => "Level Up! 🏆 Level {$newLevel}",
                'message' => "Amazing! You've just reached Level {$newLevel}. Keep up the great work to unlock even more features.",
                'type' => 'level',
                'action_text' => 'View My Achievements',
                'action_url' => route('author.dashboard'),
            ]));
        }
        
        $user->save();
    }

    public function getProgress(User $user): array
    {
        $currentLevelXp = $this->getXpForLevel($user->level);
        $nextLevelXp = $this->getXpForLevel($user->level + 1);
        
        $xpInCurrentLevel = $user->xp - $currentLevelXp;
        $xpRequiredForNextLevel = $nextLevelXp - $currentLevelXp;
        
        $percentage = $xpRequiredForNextLevel > 0 
            ? min(100, ($xpInCurrentLevel / $xpRequiredForNextLevel) * 100) 
            : 100;

        return [
            'level' => $user->level,
            'xp' => $user->xp,
            'next_level_xp' => $nextLevelXp,
            'percentage' => (int) $percentage,
            'remaining_xp' => $nextLevelXp - $user->xp,
            'discount' => $this->getCommissionDiscount($user),
        ];
    }

    /**
     * Calculate the fee discount based on Level.
     * -0.5% for every 5 levels, max -5.0%.
     */
    public function getCommissionDiscount(User $user): float
    {
        $bonusGroups = floor($user->level / 5);
        $discount = $bonusGroups * 0.5;
        
        return min(5.0, $discount);
    }

    /**
     * Get the final platform fee percentage.
     */
    public function getFinalCommissionRate(User $user): float
    {
        $plan = $user->currentPlan();
        $baseRate = $plan->commission_rate ?? 20.0;
        $discount = $this->getCommissionDiscount($user);
        
        return max(5.0, $baseRate - $discount);
    }
}
