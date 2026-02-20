<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawalProcessed extends Mailable
{
    use Queueable, SerializesModels;

    public $withdrawal;

    public function __construct($withdrawal)
    {
        $this->withdrawal = $withdrawal;
        $this->withdrawal->loadMissing('author');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.finance'),
            subject: 'Penarikan Dana Berhasil - Rp ' . number_format($this->withdrawal->amount, 0, ',', '.'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.author.withdrawal-processed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
