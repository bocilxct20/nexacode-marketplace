<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Dikirim ke email BARU untuk verifikasi perubahan email.
 */
class EmailChangeVerification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User   $user,
        public string $newEmail,
        public string $verificationUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✉️ Verifikasi Email Baru Kamu — NEXACODE',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.auth.email-change-verification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
