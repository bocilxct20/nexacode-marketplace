<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyDigest extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $trendingProducts;
    public $newArrivals;
    public $risingStars;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $trendingProducts, $newArrivals, $risingStars)
    {
        $this->user = $user;
        $this->trendingProducts = $trendingProducts;
        $this->newArrivals = $newArrivals;
        $this->risingStars = $risingStars;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'NexaCode Weekly: Curation for ' . now()->format('M d'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.weekly-digest',
            with: [
                'name' => $this->user->name,
                'trending' => $this->trendingProducts,
                'newArrivals' => $this->newArrivals,
                'stars' => $this->risingStars,
            ],
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
