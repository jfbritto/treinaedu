<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCancelledNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $firstName = explode(' ', $notifiable->name)[0];

        return (new MailMessage)
            ->subject('Assinatura cancelada - TreinaEdu')
            ->greeting("Olá, {$firstName}.")
            ->line('Sua assinatura do TreinaEdu foi **cancelada** conforme solicitado.')
            ->line('A partir de agora, o acesso à plataforma será limitado. Os dados da sua empresa serão mantidos por 30 dias caso deseje reativar.')
            ->line('Se foi um engano ou mudou de ideia, é fácil reativar:')
            ->action('Reativar Assinatura', url('/subscription/plans'))
            ->line('Esperamos ter você de volta em breve!')
            ->salutation('Equipe TreinaEdu');
    }
}
