<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset Your Password — '.config('app.name'))
            ->view('auth.emails.reset-password', [
                'resetUrl' => $resetUrl,
                'userName' => $notifiable->name,
                'appName' => config('app.name'),
                'expireMinutes' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60),
            ]);
    }
}
