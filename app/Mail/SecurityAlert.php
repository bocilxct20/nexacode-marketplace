<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SecurityAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $title;
    public $messageBody;
    public $actionUrl;
    public $actionText;

    public function __construct(User $user, string $title, string $messageBody, string $actionUrl = null, string $actionText = null)
    {
        $this->user = $user;
        $this->title = $title;
        $this->messageBody = $messageBody;
        $this->actionUrl = $actionUrl ?? route('security.dashboard');
        $this->actionText = $actionText ?? 'Tinjau Keamanan';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.security'),
            subject: $this->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.security.alert',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
