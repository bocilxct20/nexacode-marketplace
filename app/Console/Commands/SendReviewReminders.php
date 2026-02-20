<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendReviewReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-review-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders to buyers 5-7 days post-purchase to encourage product reviews';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for orders eligible for review reminders...');

        // Find completed orders between 5 and 7 days old that haven't been reminded
        $orders = \App\Models\Order::where('status', \App\Models\Order::STATUS_COMPLETED)
            ->where('paid_at', '<=', now()->subDays(5))
            ->where('paid_at', '>=', now()->subDays(7))
            ->whereNull('review_reminded_at')
            ->with(['buyer', 'items.product.reviews'])
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No eligible orders found for review reminders.');
            return;
        }

        $count = 0;
        foreach ($orders as $order) {
            if (!$order->buyer || !$order->buyer->wantsEmail('review_notifications')) {
                continue;
            }

            // Check if user has already reviewed ALL items in this order
            $allItemsReviewed = true;
            foreach ($order->items as $item) {
                $hasReview = $item->product->reviews()
                    ->where('buyer_id', $order->buyer_id)
                    ->exists();
                
                if (!$hasReview) {
                    $allItemsReviewed = false;
                    break;
                }
            }

            if ($allItemsReviewed) {
                // Silently mark as reminded to avoid checking again
                $order->update(['review_reminded_at' => now()]);
                continue;
            }

            try {
                // Find the first product in this order that hasn't been reviewed yet
                $unreviewedItem = $order->items->first(function ($item) use ($order) {
                    return !$item->product->reviews()
                        ->where('buyer_id', $order->buyer_id)
                        ->exists();
                });

                if ($unreviewedItem) {
                    \Illuminate\Support\Facades\Mail::to($order->buyer->email)
                        ->queue(new \App\Mail\PostPurchaseReviewReminder($order->buyer, $unreviewedItem->product));

                    $order->update(['review_reminded_at' => now()]);
                    $count++;
                }
            } catch (\Exception $e) {
                $this->error("Failed to send review reminder for Order #{$order->transaction_id}: " . $e->getMessage());
            }
        }

        $this->info("Successfully queued {$count} post-purchase review reminders.");
    }
}
