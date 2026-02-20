<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email dikirim saat ada login dari IP atau device baru.
 * Memungkinkan user mengetahui jika ada akses tidak sah.
 */
class NewLoginAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User   $user,
        public string $ip,
        public string $device,
        public string $loginAt,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 Login Baru Terdeteksi — NEXACODE',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.auth.new-login-alert',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
