<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductUpdateNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $product;
    public $version;

    /**
     * Create a new message instance.
     */
    public function __construct(\App\Models\Product $product, \App\Models\ProductVersion $version)
    {
        $this->product = $product;
        $this->version = $version;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.notification'),
            subject: 'Update Available - ' . ($this->product->name ?? 'Product') . ' v' . ($this->version->version_number ?? '1.0'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.product.update-notification',
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
