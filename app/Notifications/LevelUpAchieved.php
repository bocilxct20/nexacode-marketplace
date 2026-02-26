<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LevelUpAchieved extends Notification
{
    use Queueable;

    public $level;

    /**
     * Create a new notification instance.
     */
    public function __construct($level)
    {
        $this->level = $level;
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
            ->subject('Naik Level! - NexaCode Gamification')
            ->greeting('Selamat, ' . $notifiable->name . '!')
            ->line('Kamu baru saja naik ke **Level ' . $this->level . '**!')
            ->line('Terus aktif di NexaCode untuk mendapatkan lebih banyak XP dan membuka badge eksklusif.')
            ->action('Lihat Dashboard Saya', url('/dashboard'))
            ->line('Build extraordinary things with NEXACODE!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'level_up',
            'level' => $this->level,
            'message' => 'Selamat! Kamu naik ke Level ' . $this->level,
        ];
    }
}
