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
        
        \Illuminate\Support\Facades\Log::info("Author XP Added: User #{$user->id} (+{$amount} XP). Total: {$user->xp}");

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

    public function deductXp(User $user, int $amount): void
    {
        $user->xp = max(0, $user->xp - $amount);
        
        \Illuminate\Support\Facades\Log::info("Author XP Deducted: User #{$user->id} (-{$amount} XP). Total: {$user->xp}");

        // Recalculate level if XP drops significantly
        $newLevel = $this->getLevelForXp($user->xp);
        
        if ($newLevel < $user->level) {
            $user->level = $newLevel;
            \Illuminate\Support\Facades\Log::warning("Level Down! User #{$user->id} is now Level {$newLevel} due to refund.");
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
            'discount' => $this->getCommissionDiscountMultiplier($user) * 100, // Show as percentage (e.g. 5.0)
        ];
    }

    /**
     * Calculate the fee discount percentage based on Level.
     * Rewards: 0.5% reduction of the base fee for every 5 levels, max 5.0% total reduction.
     * Example: If base fee is 10%, a 5% reduction makes it 9.5%.
     */
    public function getCommissionDiscountMultiplier(User $user): float
    {
        // 0.5% proportional reduction for every 5 levels
        $bonusGroups = floor($user->level / 5);
        $discountPercentage = $bonusGroups * 0.005; // 0.5% expressed as decimal 0.005
        
        // Cap at 5% total reduction (multiplier of 0.05)
        return min(0.05, $discountPercentage);
    }

    /**
     * Get the final platform fee percentage.
     * Calculated as: Base Rate * (1 - Level Discount Multiplier)
     */
    public function getFinalCommissionRate(User $user): float
    {
        $plan = $user->currentPlan();
        $baseRate = $plan->commission_rate ?? 20.0;
        
        $discountMultiplier = $this->getCommissionDiscountMultiplier($user);
        
        // Final Rate = Base Rate * (1 - Proportional Discount)
        // e.g., 10% * (1 - 0.05) = 9.5%
        $finalRate = $baseRate * (1 - $discountMultiplier);
        
        return (float) $finalRate;
    }
}
