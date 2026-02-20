<?php

namespace App\Mail;

use App\Models\Payout;
use App\Models\AffiliatePayout;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawalRequested extends Mailable
{
    use Queueable, SerializesModels;

    public $withdrawal;

    public function __construct($withdrawal)
    {
        $this->withdrawal = $withdrawal;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.finance'),
            subject: 'Permintaan Penarikan Dana Diterima',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payouts.requested',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
