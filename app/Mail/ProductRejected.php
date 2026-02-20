<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $product;
    public $reason;

    public function __construct(Product $product, string $reason = '')
    {
        $this->product = $product;
        $this->product->loadMissing(['author']);
        $this->reason = $reason ?: 'Tidak memenuhi kriteria kualitas.';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.support'),
            subject: 'Product Rejected - ' . ($this->product->name ?? 'Your Product'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.author.product-rejected',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
