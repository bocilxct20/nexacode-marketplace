<?php

namespace App\Mail;

use App\Models\RefundRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RefundRequestResolved extends Mailable
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
        $statusText = $this->refundRequest->status === 'approved' ? 'Disetujui' : 'Ditolak';
        return new Envelope(
            from: config('mail.aliases.billing'),
            subject: 'Update Refund: Pengajuan Kamu ' . $statusText,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.refunds.resolved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
