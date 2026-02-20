<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RemindAbandonedCheckouts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remind-abandoned-checkouts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for abandoned checkouts (orders pending > 2 hours)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for abandoned checkouts...');

        $orders = \App\Models\Order::whereIn('status', [\App\Models\Order::STATUS_PENDING, \App\Models\Order::STATUS_PENDING_PAYMENT])
            ->where('created_at', '<=', now()->subHours(2))
            ->where('reminder_count', 0)
            ->with(['buyer', 'items.product'])
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No abandoned checkouts found.');
            return;
        }

        $count = 0;
        foreach ($orders as $order) {
            if (!$order->buyer || empty($order->buyer->email)) {
                continue;
            }

            // Check preference for marketing/reminders
            if (!$order->buyer->wantsEmail('marketing_emails')) {
                continue;
            }

            try {
                \Illuminate\Support\Facades\Mail::to($order->buyer->email)
                    ->queue(new \App\Mail\AbandonedCheckoutReminder($order));

                $order->update([
                    'last_reminded_at' => now(),
                    'reminder_count' => $order->reminder_count + 1,
                ]);

                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for Order #{$order->transaction_id}: " . $e->getMessage());
            }
        }

        $this->info("Successfully queued {$count} abandoned checkout reminders.");
    }
}
