<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmedNotification extends Notification
{
    public function __construct(private float $amount) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $firstName = explode(' ', $notifiable->name)[0];
        $formattedAmount = 'R$ ' . number_format($this->amount, 2, ',', '.');

        return (new MailMessage)
            ->subject('Pagamento confirmado - TreinaEdu')
            ->greeting("Olá, {$firstName}!")
            ->line("Confirmamos o recebimento do seu pagamento de **{$formattedAmount}**.")
            ->line('Sua assinatura está **ativa** e todos os recursos da plataforma continuam disponíveis para sua equipe.')
            ->action('Acessar o Dashboard', url('/dashboard'))
            ->line('Obrigado por confiar no TreinaEdu para capacitar sua equipe!')
            ->salutation('Equipe TreinaEdu');
    }
}
