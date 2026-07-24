<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    /**
     * Branded replacement for the framework's default verification email.
     * The signed URL generation is inherited from the parent class.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verify Your Email — '.config('app.name'))
            ->view('auth.emails.verify-email', [
                'verificationUrl' => $this->verificationUrl($notifiable),
                'userName' => $notifiable->name,
                'appName' => config('app.name'),
                'expireMinutes' => config('auth.verification.expire', 60),
            ]);
    }
}
