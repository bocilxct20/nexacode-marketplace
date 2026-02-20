<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckGrowthMilestones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-growth-milestones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for growth milestones and anniversaries...');

        // 1. Check for Anniversaries (Users joined on this day in previous years)
        $this->checkAnniversaries();

        // 2. Check for Author Sales Milestones
        $this->checkSalesMilestones();

        $this->info('Growth milestone check completed.');
    }

    private function checkAnniversaries()
    {
        $today = now()->format('m-d');
        
        // Find users whose creation month/day matches today, excluding recent joins (less than 1 year)
        $users = \App\Models\User::whereRaw("DATE_FORMAT(created_at, '%m-%d') = ?", [$today])
            ->where('created_at', '<=', now()->subYear())
            ->where(function ($query) {
                $query->whereNull('last_anniversary_sent_at')
                    ->orWhere('last_anniversary_sent_at', '<', now()->subMonths(11));
            })
            ->get();

        foreach ($users as $user) {
            if (!$user->wantsEmail('marketing_emails')) continue;

            $years = now()->diffInYears($user->created_at);
            
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)
                    ->queue(new \App\Mail\AnniversaryCelebration($user, 'anniversary', ['years' => $years]));
                
                $user->update(['last_anniversary_sent_at' => now()]);
            } catch (\Exception $e) {
                $this->error("Failed to send anniversary for {$user->email}: " . $e->getMessage());
            }
        }
    }

    private function checkSalesMilestones()
    {
        $milestones = [10, 50, 100, 500, 1000, 5000, 10000];

        // Find authors (users with at least 1 product)
        $authors = \App\Models\User::whereHas('products')->get();

        foreach ($authors as $author) {
            if (!$author->wantsEmail('marketing_emails')) continue;

            // Count completed sales
            $salesCount = \App\Models\Order::whereHas('items.product', function ($query) use ($author) {
                $query->where('author_id', $author->id);
            })->where('status', \App\Models\Order::STATUS_COMPLETED)->count();

            // Find highest reached milestone that hasn't been celebrated yet
            $reachedMilestone = 0;
            foreach ($milestones as $milestone) {
                if ($salesCount >= $milestone && $author->last_milestone_reached < $milestone) {
                    $reachedMilestone = $milestone;
                }
            }

            if ($reachedMilestone > 0) {
                try {
                    \Illuminate\Support\Facades\Mail::to($author->email)
                        ->queue(new \App\Mail\AnniversaryCelebration($author, 'milestone', ['milestone' => $reachedMilestone]));

                    $author->update(['last_milestone_reached' => $reachedMilestone]);
                    $this->info("Milestone {$reachedMilestone} reached for author: {$author->email}");
                } catch (\Exception $e) {
                    $this->error("Failed to send milestone for {$author->email}: " . $e->getMessage());
                }
            }
        }
    }
}
