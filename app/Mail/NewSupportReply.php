<?php

namespace App\Mail;

use App\Models\SupportReply;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewSupportReply extends Mailable
{
    use Queueable, SerializesModels;

    public $reply;
    public $recipientName;

    public function __construct(SupportReply $reply, $recipientName = 'User')
    {
        $this->reply = $reply;
        $this->recipientName = $recipientName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.support'),
            subject: 'Balasan Baru pada Tiket Support #' . $this->reply->support_ticket_id,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.support.new-reply',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
