<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnniversaryCelebration extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $type; // 'anniversary' or 'milestone'
    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct(\App\Models\User $user, string $type, array $data = [])
    {
        $this->user = $user;
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->type === 'anniversary' 
            ? 'Happy Anniversary - ' . ($this->data['years'] ?? 1) . ' Years with NexaCode'
            : 'Congratulations! New Milestone Achieved: ' . ($this->data['milestone'] ?? '') . ' Sales';

        return new Envelope(
            from: config('mail.aliases.notification'),
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.users.anniversary',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
