<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(config('app.url').route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false));
        $expire = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->from(config('mail.aliases.security'), 'NexaCode Security')
            ->subject('Reset Password Akun NexaCode Kamu')
            ->markdown('emails.security', [
                'title' => '🔐 Reset Password Kamu',
                'actionText' => 'Reset Password',
                'actionUrl' => $url,
                'content' => "Halo <strong>{$notifiable->name}</strong>,<br><br>Kamu menerima email ini karena kami menerima permintaan reset password untuk akun kamu. Jika kamu merasa tidak melakukan permintaan ini, abaikan saja email ini.",
                'metadata' => "Link reset password ini akan kadaluarsa dalam <strong>{$expire} menit</strong>. Demi keamanan, jangan bagikan link ini kepada siapapun."
            ]);
    }
}
