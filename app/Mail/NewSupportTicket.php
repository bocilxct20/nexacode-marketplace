<?php

namespace App\Mail;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewSupportTicket extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.support'),
            subject: 'Tiket Support Baru: ' . $this->ticket->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.support.new-ticket',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
