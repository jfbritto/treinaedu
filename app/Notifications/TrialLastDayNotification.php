<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialLastDayNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $firstName = explode(' ', $notifiable->name)[0];

        return (new MailMessage)
            ->subject('Último dia do seu teste gratuito - TreinaEdu')
            ->greeting("Atenção, {$firstName}!")
            ->line('Seu período de **teste gratuito termina hoje**.')
            ->line('A partir de amanhã, o acesso à plataforma será bloqueado para toda sua equipe.')
            ->line('**Seus dados e progresso serão preservados por 30 dias.** Basta assinar um plano para reativar tudo instantaneamente.')
            ->action('Assinar agora', url('/subscription/plans'))
            ->line('Precisa de ajuda para escolher? Responda este e-mail.')
            ->salutation('Equipe TreinaEdu');
    }
}
