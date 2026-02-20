<?php

namespace App\Mail;

use App\Models\RefundRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewRefundRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $refundRequest;

    public function __construct(RefundRequest $refundRequest)
    {
        $this->refundRequest = $refundRequest;
        $this->refundRequest->loadMissing(['order', 'user']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.billing'),
            subject: 'Pengajuan Refund Baru: Order #' . $this->refundRequest->order_id,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.refunds.new-request',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
