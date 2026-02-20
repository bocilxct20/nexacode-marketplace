<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewReplyNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
        $this->review->loadMissing(['product']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.support'),
            subject: 'Penjual Membalas Review Kamu!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reviews.reply',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
