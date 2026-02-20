<?php

namespace App\Mail;

use App\Models\AuthorRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewAuthorApplication extends Mailable
{
    use Queueable, SerializesModels;

    public $authorRequest;
    public $user;

    public function __construct(AuthorRequest $authorRequest)
    {
        $this->authorRequest = $authorRequest;
        $this->authorRequest->loadMissing('user');
        $this->user = $this->authorRequest->user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.admin'),
            subject: 'Pengajuan Author Baru dari ' . $this->user->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.new-author-application',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
