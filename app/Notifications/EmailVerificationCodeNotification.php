<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationCodeNotification extends Notification
{
    public function __construct(private string $code) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $firstName = explode(' ', $notifiable->name)[0];

        return (new MailMessage)
            ->subject("Seu código de verificação: {$this->code} - TreinaEdu")
            ->greeting("Olá, {$firstName}!")
            ->line('Use o código abaixo para verificar seu e-mail e ativar sua conta:')
            ->line("# {$this->code}")
            ->line('Este código expira em **10 minutos**.')
            ->line('Se você não criou uma conta no TreinaEdu, ignore este e-mail.')
            ->salutation('Equipe TreinaEdu');
    }
}
