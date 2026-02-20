<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Earning;
use App\Models\AffiliateEarning;
use App\Enums\OrderStatus;
use App\Enums\EarningStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Notifications\SystemNotification;

class OrderFulfillmentService
{
    protected $authorLevelService;

    public function __construct(AuthorLevelService $authorLevelService)
    {
        $this->authorLevelService = $authorLevelService;
    }

    /**
     * Finalize the order completion.
     */
    public function complete(Order $order): void
    {
        if ($order->status === OrderStatus::COMPLETED) {
            return;
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'status' => OrderStatus::COMPLETED,
                'paid_at' => now(),
            ]);

            if ($order->type === 'subscription') {
                $this->handleSubscription($order);
            } else {
                $this->handleProductFulfillment($order);
            }
        });
    }

    /**
     * Handle subscription fulfillment.
     */
    protected function handleSubscription(Order $order): void
    {
        $item = $order->items()->whereNotNull('subscription_plan_id')->first();
        if (!$item) {
            Log::warning('Subscription order missing plan item: ' . $order->id);
            return;
        }

        $expiryDate = now()->addDays(30);
        $order->buyer->update([
            'subscription_plan_id' => $item->subscription_plan_id,
            'trial_ends_at' => null,
            'subscription_ends_at' => $expiryDate,
        ]);

        // Send Email
        try {
            $mailable = new \App\Mail\SubscriptionActivated($order);
            if ($mailable->plan) {
                Mail::to($order->buyer->email)->send($mailable);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send subscription email (Order #' . $order->id . '): ' . $e->getMessage());
        }

        // Notification
        $order->buyer->notify(new SystemNotification([
            'title' => 'Subscription Activated! 🚀',
            'message' => "Pembayaran untuk paket {$item->subscriptionPlan->name} telah berhasil diverifikasi. Akun kamu sudah premium!",
            'type' => 'payment',
            'action_text' => 'Buka Dashboard',
            'action_url' => route('author.dashboard'),
        ]));
    }

    /**
     * Handle standard product fulfillment.
     */
    protected function handleProductFulfillment(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;
            $author = $product->author;
            
            if (!$author) continue;

            $this->calculateEarnings($order, $item);

            // Notify Author
            $author->notify(new SystemNotification([
                'title' => 'New Sale! 💰',
                'message' => "Congratulations! You just earned a sale from \"{$product->name}\".",
                'type' => 'sale',
                'action_text' => 'View Earnings',
                'action_url' => route('author.earnings'),
            ]));

            // Email Author
            try {
                Mail::to($author->email)->queue(new \App\Mail\NewSaleNotification($item));
            } catch (\Exception $e) {
                Log::error('Failed to send new sale notification to author: ' . $e->getMessage());
            }
        }

        // Notify Buyer
        $order->buyer->notify(new SystemNotification([
            'title' => 'Payment Verified! ✅',
            'message' => 'Pembayaran for order ' . $order->transaction_id . ' has been verified.',
            'type' => 'payment',
            'action_text' => 'Lihat Pembelian',
            'action_url' => route('purchases.index'),
        ]));

        // Email Buyer
        try {
            Mail::to($order->buyer->email)->queue(new \App\Mail\DownloadReceipt($order));
        } catch (\Exception $e) {
            Log::error('Failed to send download receipt email: ' . $e->getMessage());
        }
    }

    /**
     * Calculate and create earnings for author and affiliate.
     */
    protected function calculateEarnings(Order $order, $item): void
    {
        $product = $item->product;
        
        // Capture snapshot if product exists
        if ($product) {
            $item->update([
                'product_name' => $product->name,
                'product_thumbnail' => $product->thumbnail,
            ]);
        }

        $author = $product ? $product->author : null;
        if (!$author) {
            Log::warning('OrderItem missing author for earning calculation: ' . $item->id);
            return;
        }
        $commissionRate = $this->authorLevelService->getFinalCommissionRate($author);
        
        $itemPrice = $item->price; // Original/Gross price
        
        if ($order->discount_amount > 0) {
            // Pro-rate the discount across items based on their price ratio
            $totalItemsPrice = $order->items->sum('price');
            if ($totalItemsPrice > 0) {
                $proportion = $itemPrice / $totalItemsPrice;
                $itemDiscount = $order->discount_amount * $proportion;
                $itemPrice = max(0, $itemPrice - $itemDiscount);
            }
        }

        $commissionAmount = ($itemPrice * $commissionRate) / 100;
        $authorAmount = $itemPrice - $commissionAmount;

        Earning::create([
            'product_id' => $item->product_id,
            'order_id' => $order->id,
            'author_id' => $author->id,
            'amount' => $authorAmount,
            'commission_amount' => $commissionAmount,
            'status' => EarningStatus::PENDING,
            'available_at' => now()->addDay(),
        ]);

        // Track sale for analytics
        $product->trackSale($authorAmount);

        // Award XP
        $xpAmount = (int) ($authorAmount / 1000);
        if ($xpAmount > 0) {
            $this->authorLevelService->addXp($author, $xpAmount);
        }

        // Handle Affiliate
        if ($order->affiliate_id) {
            $this->handleAffiliateCommission($order, $item, $itemPrice, $commissionAmount);
        }
    }

    /**
     * Handle affiliate commission calculation.
     */
    protected function handleAffiliateCommission(Order $order, $item, $itemPrice, $platformCommission): void
    {
        $affiliate = $order->affiliate;
        $affiliateRate = 10.00; // Fixed 10%
        $affiliateAmount = ($itemPrice * $affiliateRate) / 100;
        $affiliateAmount = min($affiliateAmount, $platformCommission);

        if ($affiliateAmount > 0) {
            $affiliateEarning = AffiliateEarning::create([
                'user_id' => $order->affiliate_id,
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'amount' => $affiliateAmount,
                'commission_rate' => $affiliateRate,
                'status' => 'completed',
            ]);

            $affiliate->notify(new SystemNotification([
                'title' => 'Affiliate Commission! 🛡️',
                'message' => "You earned Rp " . number_format($affiliateAmount, 0, ',', '.') . " from a referral.",
                'type' => 'affiliate',
                'action_text' => 'View Balance',
                'action_url' => route('affiliate.dashboard'),
            ]));

            try {
                Mail::to($affiliate->email)->queue(new \App\Mail\CommissionEarned($affiliateEarning));
            } catch (\Exception $e) {
                Log::error('Failed to send affiliate commission email: ' . $e->getMessage());
            }
        }
    }
}
