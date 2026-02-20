<?php

namespace App\Mail;

use App\Models\AffiliateEarning;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CommissionEarned extends Mailable
{
    use Queueable, SerializesModels;

    public $earning;

    public function __construct(AffiliateEarning $earning)
    {
        $this->earning = $earning;
        $this->earning->loadMissing(['product']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.affiliates'),
            subject: 'Hore! Kamu Baru Saja Mendapat Komisi!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.affiliates.commission-earned',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
