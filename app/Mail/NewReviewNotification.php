<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewReviewNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
        // Eager load relationships needed for the view
        $this->review->loadMissing(['product', 'buyer']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.support'),
            subject: 'New Review on ' . ($this->review->product->name ?? 'Your Product'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.author.new-review',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
