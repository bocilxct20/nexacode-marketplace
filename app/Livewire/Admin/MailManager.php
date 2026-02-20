<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVersion;
use App\Models\User;
use App\Models\SupportTicket;
use App\Models\Review;
use App\Models\Withdrawal;
use App\Models\AuthorRequest;
use App\Models\AffiliateEarning;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Flux;

class MailManager extends Component
{
    public function sendTest($type)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $admin = auth()->user();
        
        try {
            $this->handleMailDispatch($type, $admin);
            Flux::toast(variant: 'success', heading: 'Email Sent', text: "Test email for \"{$type}\" has been sent to your inbox.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("MailManager Error: " . $e->getMessage());
            Flux::toast(variant: 'danger', heading: 'Error', text: "Failed to send test email: " . $e->getMessage());
        }
    }

    protected function handleMailDispatch($type, $user)
    {
        switch ($type) {
            // Transactional & Commerce
            case 'order_confirmation':
                Mail::to($user->email)->send(new \App\Mail\OrderConfirmation($this->getDummyOrder($user)));
                break;
            case 'SubscriptionActivated':
                Mail::to($user->email)->send(new \App\Mail\SubscriptionActivated($this->getDummyOrder($user, 'subscription')));
                break;
            case 'TrialStarted':
                $user->trial_ends_at = now()->addDays(7);
                Mail::to($user->email)->send(new \App\Mail\TrialStarted($user, \App\Models\SubscriptionPlan::getDefaultPlan()));
                break;
            case 'download_receipt':
                Mail::to($user->email)->send(new \App\Mail\DownloadReceipt($this->getDummyOrder($user)));
                break;
            case 'abandoned_checkout':
                Mail::to($user->email)->send(new \App\Mail\AbandonedCheckoutReminder($this->getDummyOrder($user)));
                break;
            case 'new_sale':
                Mail::to($user->email)->send(new \App\Mail\NewSaleNotification($this->getDummyOrderItem($user)));
                break;
            case 'commission_earned':
                Mail::to($user->email)->send(new \App\Mail\CommissionEarned($this->getDummyAffiliateEarning($user)));
                break;

            // Support & Refunds
            case 'new_support_ticket':
                Mail::to($user->email)->send(new \App\Mail\NewSupportTicket($this->getDummyTicket($user)));
                break;
            case 'new_support_reply':
                Mail::to($user->email)->send(new \App\Mail\NewSupportReply($this->getDummySupportReply($user)));
                break;
            case 'new_refund_request':
                Mail::to($user->email)->send(new \App\Mail\NewRefundRequest($this->getDummyRefundRequest($user)));
                break;
            case 'refund_resolved':
                Mail::to($user->email)->send(new \App\Mail\RefundRequestResolved($this->getDummyRefundRequest($user)));
                break;

            // Moderation
            case 'product_submission':
                Mail::to($user->email)->send(new \App\Mail\AdminNewProductSubmission($this->getDummyProduct($user)));
                break;
            case 'product_approved':
                Mail::to($user->email)->send(new \App\Mail\ProductApproved($this->getDummyProduct($user)));
                break;
            case 'product_rejected':
                Mail::to($user->email)->send(new \App\Mail\ProductRejected($this->getDummyProduct($user), 'Logo tidak sesuai standar kualitas NEXACODE.'));
                break;
            case 'author_application':
                Mail::to($user->email)->send(new \App\Mail\AdminNewAuthorApplication($this->getDummyAuthorRequest($user)));
                break;
            case 'author_approved':
                Mail::to($user->email)->send(new \App\Mail\AuthorApplicationApproved($this->getDummyAuthorRequest($user)));
                break;
            case 'author_rejected':
                Mail::to($user->email)->send(new \App\Mail\AuthorApplicationRejected($this->getDummyAuthorRequest($user), 'Portfolio belum mencukupi standar teknis kami.'));
                break;

            // Community & Marketing
            case 'weekly_digest':
                $products = Product::approved()->take(3)->get();
                Mail::to($user->email)->send(new \App\Mail\WeeklyDigest($user, $products, $products, User::take(2)->get()));
                break;
            case 'newsletter':
                $content = '<h2>Monthly Marketplace Updates</h2><p>Here are the latest tips and news to help you succeed on NexaCode Marketplace.</p><ul><li>New features released this month</li><li>Best practices for product listings</li><li>Success stories from top authors</li></ul>';
                Mail::to($user->email)->send(new \App\Mail\Newsletter($user, 'Monthly Marketplace Updates', $content));
                break;
            case 'welcome':
                Mail::to($user->email)->send(new \App\Mail\WelcomeEmail($user));
                break;

            // Users & Security
            case 'otp':
                Mail::to($user->email)->send(new \App\Mail\OtpVerification('123456'));
                break;
            case 'security_alert':
                Mail::to($user->email)->send(new \App\Mail\SecurityAlert($user, 'Login dari perangkat baru (Jakarta, ID)'));
                break;
            case 'anniversary':
                Mail::to($user->email)->send(new \App\Mail\AnniversaryCelebration($user, 'anniversary', ['years' => 1]));
                break;

            // Reviews
            case 'new_review':
                Mail::to($user->email)->send(new \App\Mail\NewReviewNotification($this->getDummyReview($user)));
                break;
            case 'review_reminder':
                Mail::to($user->email)->send(new \App\Mail\PostPurchaseReviewReminder($user, $this->getDummyProduct($user)));
                break;
            case 'review_reply':
                Mail::to($user->email)->send(new \App\Mail\ReviewReplyNotification($this->getDummyReview($user)));
                break;

            // Financial
            case 'withdrawal_requested':
                Mail::to($user->email)->send(new \App\Mail\WithdrawalRequested($this->getDummyWithdrawal($user)));
                break;
            case 'withdrawal_processed':
                Mail::to($user->email)->send(new \App\Mail\WithdrawalProcessed($this->getDummyWithdrawal($user)));
                break;
            case 'withdrawal_rejected':
                Mail::to($user->email)->send(new \App\Mail\WithdrawalRejected($this->getDummyWithdrawal($user), 'Data bank tidak valid.'));
                break;

            // Updates
            case 'product_update':
                $p = $this->getDummyProduct($user);
                $v = $p->versions()->latest()->first() ?? new ProductVersion(['version_number' => '2.0.0', 'changelog' => 'Bug fixes and performance improvements.']);
                Mail::to($user->email)->send(new \App\Mail\ProductUpdateNotification($p, $v));
                break;

            default:
                throw new \Exception("Unknown mail type: {$type}");
        }
    }


    // Dummy Data Generators

    protected function getDummyOrderItem($user) {
        $item = OrderItem::with(['product', 'order'])->latest()->first() ?? new OrderItem([
            'product_id' => $this->getDummyProduct($user)->id,
            'price' => 150000,
            'quantity' => 1,
            'commission_rate' => 15,
        ]);

        if (!$item->exists) {
            $product = $this->getDummyProduct($user);
            $order = $this->getDummyOrder($user);
            
            $item->setRelation('product', $product);
            $item->setRelation('order', $order);
            $item->created_at = now();
            $item->exists = true;
        }

        return $item;
    }

    protected function getDummyProduct($user) {
        $product = Product::first() ?? new Product([
            'id' => 1,
            'name' => 'Premium Laravel Marketplace Kit',
            'slug' => 'premium-laravel-marketplace-kit',
            'author_id' => $user->id,
            'price' => 250000,
        ]);

        if (!$product->exists) {
            $product->created_at = now();
            $product->exists = true;
        }

        return $product;
    }

    protected function getDummyOrder($user) {
        $product = $this->getDummyProduct($user);
        
        $order = new Order([
            'id' => 12345,
            'transaction_id' => 'TXN-' . strtoupper(uniqid()),
            'buyer_id' => $user->id,
            'total_amount' => 299000,
            'status' => 'completed',
            'payment_method' => 'midtrans',
        ]);
        
        // Set timestamps manually as Carbon instances
        $order->created_at = now();
        $order->updated_at = now();
        $order->exists = true; // Mark as existing to prevent save attempts
        
        // Create order item with product
        $item = new OrderItem([
            'product_id' => $product->id,
            'price' => 299000,
            'commission_rate' => 15,
        ]);
        
        $item->created_at = now();
        $item->updated_at = now();
        $item->exists = true;
        
        // Set the product relationship
        $item->setRelation('product', $product);
        $item->setRelation('order', $order);
        
        // Set items relationship on order
        $order->setRelation('items', collect([$item]));
        $order->setRelation('buyer', $user);
        
        return $order;
    }

    protected function getDummySubscriptionOrder($user) {
        $plan = \App\Models\SubscriptionPlan::first() ?? \App\Models\SubscriptionPlan::getDefaultPlan();
        
        $order = new Order([
            'id' => 12346,
            'transaction_id' => 'SUB-' . strtoupper(uniqid()),
            'buyer_id' => $user->id,
            'total_amount' => $plan->price ?? 99000,
            'status' => 'completed',
            'payment_method' => 'midtrans',
        ]);
        
        $order->created_at = now();
        $order->updated_at = now();
        $order->exists = true;
        
        // Create subscription order item
        $item = new OrderItem([
            'subscription_plan_id' => $plan->id,
            'price' => $plan->price ?? 99000,
            'commission_rate' => 0,
        ]);
        
        $item->created_at = now();
        $item->updated_at = now();
        $item->exists = true;
        
        // Set the subscription plan relationship
        $item->setRelation('subscriptionPlan', $plan);
        $item->setRelation('order', $order);
        
        // Set buyer with subscription_ends_at
        $user->subscription_ends_at = now()->addMonth();
        
        // Set items and buyer relationship on order
        $order->setRelation('items', collect([$item]));
        $order->setRelation('buyer', $user);
        
        if (!$order->exists) {
            $order->created_at = now();
            $order->exists = true;
        }

        return $order;
    }

    protected function getDummyTicket($user) {
        $ticket = SupportTicket::first() ?? new SupportTicket([
            'id' => 99,
            'user_id' => $user->id,
            'subject' => 'Bantuan Instalasi Skrip',
            'status' => 'open',
            'priority' => 'high'
        ]);

        if (!$ticket->exists) {
            $ticket->created_at = now();
            $ticket->exists = true;
        }

        return $ticket;
    }

    protected function getDummyReview($user) {
        $review = Review::with(['product', 'buyer'])->first() ?? new Review([
            'id' => 1,
            'buyer_id' => $user->id,
            'product_id' => $this->getDummyProduct($user)->id,
            'rating' => 5,
            'comment' => 'Sangat bagus dan mudah digunakan!'
        ]);

        if (!$review->exists) {
            $review->created_at = now();
            $review->exists = true;
        }

        return $review;
    }

    protected function getDummyWithdrawal($user) {
        $payout = \App\Models\Payout::first() ?? new \App\Models\Payout([
            'id' => 1,
            'author_id' => $user->id,
            'amount' => 500000,
            'status' => 'pending',
            'payment_method' => 'BCA - 1234567890',
            'admin_note' => null,
        ]);

        if (!$payout->exists) {
            $payout->created_at = now();
            $payout->exists = true;
        }

        return $payout;
    }

    protected function getDummySupportReply($user) {
        $ticket = $this->getDummyTicket($user);
        
        return \App\Models\SupportReply::first() ?? new \App\Models\SupportReply([
            'id' => 1,
            'support_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => 'Terima kasih atas laporannya. Kami sedang meninjau masalah ini dan akan segera memberikan solusi.',
            'is_admin' => true,
            'created_at' => now(),
        ]);
    }

    protected function getDummyRefundRequest($user) {
        $order = $this->getDummyOrder($user);
        
        $refundRequest = \App\Models\RefundRequest::first() ?? new \App\Models\RefundRequest([
            'id' => 1,
            'order_id' => $order->id,
            'user_id' => $user->id,
            'reason' => 'Produk tidak sesuai deskripsi',
            'status' => 'pending',
        ]);
        
        // Set timestamps if creating new instance
        if (!$refundRequest->exists) {
            $refundRequest->created_at = now();
            $refundRequest->updated_at = now();
            $refundRequest->exists = true;
        }
        
        return $refundRequest;
    }

    protected function getDummyAuthorRequest($user) {
        $request = AuthorRequest::with('user')->first() ?? new AuthorRequest([
            'user_id' => $user->id,
            'portfolio_url' => 'https://github.com/nexacode',
            'message' => 'Saya ingin bergabung sebagai author untuk menjual berbagai template Laravel dan desain UI/UX berkualitas tinggi.',
            'status' => 'pending'
        ]);

        if (!$request->exists) {
            $request->setRelation('user', $user);
            $request->created_at = now();
            $request->exists = true;
        }

        return $request;
    }

    protected function getDummyAffiliateEarning($user) {
        $earning = AffiliateEarning::with(['product', 'order'])->first() ?? new AffiliateEarning([
            'user_id' => $user->id,
            'order_id' => $this->getDummyOrder($user)->id,
            'product_id' => $this->getDummyProduct($user)->id,
            'amount' => 45000,
            'commission_rate' => 15,
            'status' => 'completed',
        ]);

        if (!$earning->exists) {
            $earning->setRelation('product', $this->getDummyProduct($user));
            $earning->setRelation('order', $this->getDummyOrder($user));
            $earning->created_at = now();
            $earning->exists = true;
        }

        return $earning;
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $categories = [
            'Transactional' => [
                ['id' => 'order_confirmation', 'name' => 'Order Confirmation', 'icon' => 'check-circle'],
                ['id' => 'SubscriptionActivated', 'name' => 'Subscription Activated', 'icon' => 'sparkles'],
                ['id' => 'TrialStarted', 'name' => 'Trial Started', 'icon' => 'clock'],
                ['id' => 'download_receipt', 'name' => 'Download Receipt', 'icon' => 'arrow-down-tray'],
                ['id' => 'abandoned_checkout', 'name' => 'Checkout Recovery', 'icon' => 'shopping-cart'],
            ],
            'Support' => [
                ['id' => 'new_support_ticket', 'name' => 'New Support Ticket', 'icon' => 'ticket'],
                ['id' => 'new_support_reply', 'name' => 'New Support Reply', 'icon' => 'chat-bubble-left-right'],
                ['id' => 'new_refund_request', 'name' => 'New Refund Request', 'icon' => 'arrow-path'],
                ['id' => 'refund_resolved', 'name' => 'Refund Resolved', 'icon' => 'check-badge'],
            ],
            'Moderation' => [
                ['id' => 'product_submission', 'name' => 'Admin: New Product', 'icon' => 'puzzle-piece'],
                ['id' => 'product_approved', 'name' => 'Product Approved', 'icon' => 'check-circle'],
                ['id' => 'product_rejected', 'name' => 'Product Rejected', 'icon' => 'x-circle'],
                ['id' => 'author_application', 'name' => 'Admin: Author Req', 'icon' => 'user-plus'],
                ['id' => 'author_approved', 'name' => 'Author Approved', 'icon' => 'academic-cap'],
                ['id' => 'author_rejected', 'name' => 'Author Rejected', 'icon' => 'user-minus'],
            ],
            'User & Security' => [
                ['id' => 'welcome', 'name' => 'Welcome Email', 'icon' => 'hand-raised'],
                ['id' => 'otp', 'name' => 'OTP Verification', 'icon' => 'shield-check'],
                ['id' => 'security_alert', 'name' => 'Security Alert', 'icon' => 'exclamation-triangle'],
                ['id' => 'anniversary', 'name' => 'Anniversary Celebrate', 'icon' => 'gift'],
            ],
            'Sales & Growth' => [
                ['id' => 'new_sale', 'name' => 'New Sale Notification', 'icon' => 'banknotes'],
                ['id' => 'commission_earned', 'name' => 'Commission Earned', 'icon' => 'currency-dollar'],
                ['id' => 'weekly_digest', 'name' => 'Weekly Digest', 'icon' => 'newspaper'],
                ['id' => 'newsletter', 'name' => 'Newsletter', 'icon' => 'megaphone'],
            ],
            'Reviews & Feedback' => [
                ['id' => 'new_review', 'name' => 'New Review Alert', 'icon' => 'star'],
                ['id' => 'review_reminder', 'name' => 'Review Reminder', 'icon' => 'clock'],
                ['id' => 'review_reply', 'name' => 'Review Reply Notify', 'icon' => 'chat-bubble-bottom-center-text'],
            ],
            'Financial' => [
                ['id' => 'withdrawal_requested', 'name' => 'Withdrawal Requested', 'icon' => 'credit-card'],
                ['id' => 'withdrawal_processed', 'name' => 'Withdrawal Processed', 'icon' => 'check-badge'],
                ['id' => 'withdrawal_rejected', 'name' => 'Withdrawal Rejected', 'icon' => 'x-circle'],
            ],
            'Updates' => [
                ['id' => 'product_update', 'name' => 'Product Update Alert', 'icon' => 'bolt'],
            ],
        ];

        return view('livewire.admin.mail-manager', [
            'categories' => $categories
        ]);
    }
}
