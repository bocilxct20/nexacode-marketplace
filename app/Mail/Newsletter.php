<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Newsletter extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $title;
    public $content;
    public $ctaText;
    public $ctaUrl;

    public function __construct(\App\Models\User $user, string $title, string $content, string $ctaText = '', string $ctaUrl = '')
    {
        $this->user = $user;
        $this->title = $title;
        $this->content = $content;
        $this->ctaText = $ctaText;
        $this->ctaUrl = $ctaUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.aliases.info'),
            subject: $this->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.newsletter',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
