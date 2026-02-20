<?php

namespace App\Mail;

use App\Models\AuthorRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuthorApplicationApproved extends Mailable
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
            subject: 'Selamat! Pengajuan Akun Author Kamu Disetujui!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.author.approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
