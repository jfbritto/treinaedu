<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialExpiringNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Seu período de teste expira em 2 dias')
            ->greeting("Olá, {$notifiable->name}!")
            ->line('Seu período de teste no TreinaHub expira em 2 dias.')
            ->line('Para continuar usando a plataforma, escolha um plano de assinatura.')
            ->action('Escolher Plano', url('/subscription/plans'))
            ->line('Se tiver dúvidas, entre em contato conosco.');
    }
}
