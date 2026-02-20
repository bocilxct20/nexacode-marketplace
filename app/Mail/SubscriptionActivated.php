<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionActivated extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $plan;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        
        // Find the subscription plan item with preloading check
        $item = $order->items->whereNotNull('subscription_plan_id')->first();
        
        if (!$item) {
            // Fallback if collection is empty
            $item = $order->items()->whereNotNull('subscription_plan_id')->first();
        }

        $this->plan = $item ? $item->subscriptionPlan : null;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.sales'),
            subject: 'Subscription Activated - Welcome to ' . ($this->plan->name ?? 'Premium'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.author.subscription-activated',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
