<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRefundedNotification extends Notification
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
            ->subject('Pagamento estornado - TreinaEdu')
            ->greeting("Olá, {$firstName}.")
            ->line("O pagamento de **{$formattedAmount}** foi **estornado** com sucesso.")
            ->line('O valor será devolvido ao seu cartão de crédito conforme o prazo da operadora (geralmente 1 a 2 faturas).')
            ->action('Ver minha assinatura', url('/subscription'))
            ->line('Se tiver dúvidas, entre em contato com nosso suporte.')
            ->salutation('Equipe TreinaEdu');
    }
}
