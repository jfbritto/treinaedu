<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentOverdueNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $firstName = explode(' ', $notifiable->name)[0];

        return (new MailMessage)
            ->subject('Atenção: pagamento pendente - TreinaEdu')
            ->greeting("Olá, {$firstName}!")
            ->line('Identificamos que o pagamento da sua assinatura do **TreinaEdu** está pendente.')
            ->line('Para evitar a interrupção do acesso da sua equipe, regularize o pagamento nos próximos **7 dias**.')
            ->line('Após esse prazo, o acesso aos treinamentos, certificados e relatórios será temporariamente suspenso.')
            ->action('Regularizar Pagamento', url('/subscription/plans'))
            ->line('Se o pagamento já foi realizado, desconsidere este e-mail. Em caso de dúvidas, responda esta mensagem.')
            ->salutation('Equipe TreinaEdu');
    }
}
