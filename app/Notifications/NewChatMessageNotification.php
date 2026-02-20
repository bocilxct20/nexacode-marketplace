<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewChatMessageNotification extends Notification
{
    use Queueable;

    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $sender = $this->message->sender->name;
        $url = $this->message->is_admin 
            ? route('inbox') // Buyer side
            : route('author.chat'); // Author side

        return (new MailMessage)
            ->subject('Pesan Baru dari ' . $sender . ' - NexaCode')
            ->view('emails.security', [
                'title' => '💬 Pesan Baru Diterima',
                'actionText' => 'Balas Pesan Sekarang',
                'actionUrl' => $url,
                'content' => "Halo <strong>{$notifiable->name}</strong>,<br><br>Kamu menerima pesan baru dari <strong>{$sender}</strong>:<br><br><blockquote>\"" . \Illuminate\Support\Str::limit($this->message->message, 200) . "\"</blockquote>",
                'metadata' => 'Jangan biarkan mereka menunggu lama ya! Segera balas untuk menjaga reputasi kamu sebagai author/pembeli yang responsif.'
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
