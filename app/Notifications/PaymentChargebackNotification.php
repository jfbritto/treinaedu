<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentChargebackNotification extends Notification
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
            ->subject('Alerta: disputa de pagamento - TreinaEdu')
            ->greeting("Atenção, {$firstName}!")
            ->line("Recebemos uma **contestação (chargeback)** do seu cartão no valor de **{$formattedAmount}**.")
            ->line('Sua assinatura foi temporariamente suspensa até a resolução da disputa.')
            ->line('Se você não reconhece essa contestação, entre em contato com a operadora do cartão para resolver o mais rápido possível.')
            ->action('Regularizar assinatura', url('/subscription/plans'))
            ->line('Se precisar de ajuda, fale com nosso suporte.')
            ->salutation('Equipe TreinaEdu');
    }
}
