<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Console\Command;

class NotifyExpiringSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:check-expiring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for subscriptions expiring in 3 days and notify users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;
        
        // Find users whose subscription ends exactly in 3 days
        $users = User::whereNotNull('subscription_ends_at')
            ->whereDate('subscription_ends_at', now()->addDays(3)->toDateString())
            ->get();

        foreach ($users as $user) {
            $user->notify(new SystemNotification([
                'title' => 'Subscription Expiring Soon! ⏳',
                'message' => "Your manual subscription will expire in 3 days. Renew now to avoid losing your premium perks and storefront message.",
                'type' => 'warning',
                'action_text' => 'Renew Subscription',
                'action_url' => route('author.plans'),
            ]));
            
            $count++;
        }

        $this->info("Notified {$count} users about their expiring subscriptions.");
    }
}
