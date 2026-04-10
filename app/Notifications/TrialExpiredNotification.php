<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialExpiredNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $firstName = explode(' ', $notifiable->name)[0];

        return (new MailMessage)
            ->subject('Seu teste gratuito expirou - TreinaEdu')
            ->greeting("Olá, {$firstName}.")
            ->line('Seu período de teste gratuito no TreinaEdu **expirou**.')
            ->line('O acesso à plataforma foi temporariamente bloqueado para sua equipe.')
            ->line('**Boa notícia:** todos os seus dados, treinamentos e progresso dos colaboradores estão preservados. Ao assinar um plano, tudo volta exatamente como estava.')
            ->action('Reativar minha conta', url('/subscription/plans'))
            ->line('Os dados serão mantidos por 30 dias. Após esse período, poderão ser removidos.')
            ->salutation('Equipe TreinaEdu');
    }
}
