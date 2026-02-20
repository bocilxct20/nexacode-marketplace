<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->product->loadMissing(['author', 'category']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.notification'),
            subject: 'Product Approved - Ready to Publish',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.author.product-approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
