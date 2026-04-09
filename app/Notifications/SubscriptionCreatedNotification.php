<?php

namespace App\Notifications;

use App\Models\Plan;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCreatedNotification extends Notification
{
    public function __construct(private Plan $plan) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $firstName = explode(' ', $notifiable->name)[0];
        $formattedPrice = 'R$ ' . number_format($this->plan->price, 2, ',', '.');

        return (new MailMessage)
            ->subject('Assinatura ativada - TreinaEdu')
            ->greeting("Olá, {$firstName}!")
            ->line("Sua assinatura do plano **{$this->plan->name}** foi ativada com sucesso.")
            ->line("Valor mensal: **{$formattedPrice}** (cartão de crédito)")
            ->line('A cobrança será realizada automaticamente todo mês no seu cartão. Você receberá uma confirmação a cada pagamento processado.')
            ->action('Acessar o Dashboard', url('/dashboard'))
            ->line('Conte com a gente para capacitar sua equipe!')
            ->salutation('Equipe TreinaEdu');
    }
}
