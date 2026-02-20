<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     * $data = ['title', 'message', 'type', 'action_text', 'action_url', 'action_color']
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->data['title'])
            ->markdown('emails.generic', [
                'title' => $this->data['title'],
                'message' => $this->data['message'],
                'actionText' => $this->data['action_text'] ?? null,
                'actionUrl' => $this->data['action_url'] ?? null,
                'actionColor' => $this->data['action_color'] ?? 'primary',
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->data['title'],
            'message' => $this->data['message'],
            'type' => $this->data['type'] ?? 'info',
            'action_url' => $this->data['action_url'] ?? null,
        ];
    }
}
