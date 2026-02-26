<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuspiciousLoginDetected extends Notification
{
    use Queueable;

    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
            ->subject('Suspicious Login Detected - NexaCode')
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('We detected a login to your account from an unrecognized device or location.')
            ->line('**IP Address:** ' . ($this->data['ip'] ?? 'Unknown'))
            ->line('**Device:** ' . ($this->data['device'] ?? 'Unknown'))
            ->line('**Time:** ' . ($this->data['time'] ?? now()->toDateTimeString()))
            ->action('Secure My Account', url('/security'))
            ->line('If this was not you, please change your password immediately and secure your account.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'suspicious_login',
            'message' => 'Suspicious login detected from ' . ($this->data['device'] ?? 'unknown device'),
            'ip' => $this->data['ip'] ?? null,
            'time' => $this->data['time'] ?? null,
        ];
    }
}
