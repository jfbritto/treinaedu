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
        $firstName = explode(' ', $notifiable->name)[0];

        return (new MailMessage)
            ->subject('Seu teste gratuito termina em 2 dias - TreinaEdu')
            ->greeting("Olá, {$firstName}!")
            ->line('Seu período de **teste gratuito** no TreinaEdu termina em **2 dias**.')
            ->line('Para que sua equipe continue acessando os treinamentos, certificados e todas as funcionalidades, escolha um plano de assinatura.')
            ->line('Todos os dados e progresso da sua equipe serão mantidos ao assinar.')
            ->action('Escolher um Plano', url('/subscription/plans'))
            ->line('Tem alguma dúvida sobre os planos? Responda este e-mail e ajudaremos você a escolher o melhor para sua empresa.')
            ->salutation('Equipe TreinaEdu');
    }
}
