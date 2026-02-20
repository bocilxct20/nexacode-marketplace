<?php

namespace App\Mail;

use App\Models\AuthorRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuthorApplicationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $authorRequest;
    public $user;
    public $reason;

    public function __construct(AuthorRequest $authorRequest, $reason = null)
    {
        $this->authorRequest = $authorRequest;
        $this->authorRequest->loadMissing('user');
        $this->user = $this->authorRequest->user;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.admin'),
            subject: 'Update Mengenai Pengajuan Akun Author NexaCode',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.author.rejected',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
