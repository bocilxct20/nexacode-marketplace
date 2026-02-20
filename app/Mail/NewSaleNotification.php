<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewSaleNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $orderItem;

    public function __construct(OrderItem $orderItem)
    {
        $this->orderItem = $orderItem;
        // Eager load relationships needed for the view
        $this->orderItem->loadMissing(['product', 'order']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.sales'),
            subject: 'New Sale - ' . ($this->orderItem->product->name ?? 'Product'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.author.new-sale',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
