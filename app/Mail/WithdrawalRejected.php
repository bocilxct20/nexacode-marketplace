<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawalRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $withdrawal;
    public $reason;

    public function __construct($withdrawal, $reason = 'Informasi rekening tidak valid.')
    {
        $this->withdrawal = $withdrawal;
        $this->withdrawal->loadMissing('author');
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.finance'),
            subject: 'Penarikan Dana Ditolak - Rp ' . number_format($this->withdrawal->amount, 0, ',', '.'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.author.withdrawal-rejected',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
