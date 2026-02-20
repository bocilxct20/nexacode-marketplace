<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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

    public function toMail(object $notifiable): MailMessage
    {
        $url = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->from(config('mail.aliases.security'), 'NexaCode Security')
            ->subject('Verifikasi Alamat Email NexaCode Kamu')
            ->view('emails.security', [
                'title' => '📧 Verifikasi Email Kamu',
                'actionText' => 'Verifikasi Email',
                'actionUrl' => $url,
                'content' => "Halo <strong>{$notifiable->name}</strong>,<br><br>Terima kasih telah bergabung di NexaCode! Silakan klik tombol di bawah ini untuk memverifikasi alamat email kamu.",
                'metadata' => 'Jika kamu tidak membuat akun di NexaCode, kamu bisa mengabaikan email ini. Tautan verifikasi ini akan kadaluarsa dalam 60 menit.'
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
