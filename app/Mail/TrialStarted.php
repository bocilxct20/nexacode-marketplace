<?php

namespace App\Mail;

use App\Models\User;
use App\Models\SubscriptionPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialStarted extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $plan;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, SubscriptionPlan $plan)
    {
        $this->user = $user;
        $this->plan = $plan;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.sales'),
            subject: 'Trial Started - Enjoy Free Access to ' . $this->plan->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.author.trial-started',
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
