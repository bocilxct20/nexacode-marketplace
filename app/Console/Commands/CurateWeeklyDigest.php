<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CurateWeeklyDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:curate-weekly-digest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Curate and send the weekly marketplace digest to all active users.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting NexaCode Weekly curation...');

        // 1. Get Trending Products (High rated and recently popular)
        $trendingProducts = \App\Models\Product::approved()
            ->where('sales_count', '>', 0)
            ->orderBy('avg_rating', 'desc')
            ->orderBy('sales_count', 'desc')
            ->take(3)
            ->get();

        // 2. Get New Arrivals (Last 7 days, approved)
        $newArrivals = \App\Models\Product::approved()
            ->latest()
            ->where('created_at', '>=', now()->subDays(7))
            ->take(3)
            ->get();

        // 3. Get Rising Stars (Authors joined in last 60 days with at least 1 sale)
        $risingStars = \App\Models\User::where('created_at', '>=', now()->subDays(60))
            ->whereHas('products', function($query) {
                $query->approved()->where('sales_count', '>', 0);
            })
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(2)
            ->get();

        // 4. Collect Recipients (Users + Newsletter Subscribers)
        $recipients = collect();

        // Add registered users
        \App\Models\User::all()->each(function($user) use ($recipients) {
            if (!method_exists($user, 'wantsEmail') || $user->wantsEmail('weekly_digest')) {
                $recipients->put($user->email, (object)[
                    'email' => $user->email,
                    'name' => $user->name,
                    'user' => $user
                ]);
            }
        });

        // Add independent newsletter subscribers (avoiding duplicates)
        \App\Models\NewsletterSubscriber::where('status', 'active')->get()->each(function($subscriber) use ($recipients) {
            if (!$recipients->has($subscriber->email)) {
                $recipients->put($subscriber->email, (object)[
                    'email' => $subscriber->email,
                    'name' => 'Developer', // Fallback name for non-users
                    'user' => null
                ]);
            }
        });

        if ($recipients->isEmpty()) {
            $this->warn('No active subscribers or users to send the digest to.');
            return;
        }

        $count = 0;
        foreach ($recipients as $recipient) {
            \Illuminate\Support\Facades\Mail::to($recipient->email)->send(new \App\Mail\WeeklyDigest(
                $recipient,
                $trendingProducts,
                $newArrivals,
                $risingStars
            ));
            $count++;
        }

        $this->info("NexaCode Weekly sent to {$count} recipients.");
    }
}
