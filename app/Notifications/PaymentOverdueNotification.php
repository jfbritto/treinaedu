<?php
namespace App\Notifications;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
class PaymentOverdueNotification extends Notification {
    public function via($notifiable): array { return ['mail']; }
    public function toMail($notifiable): MailMessage {
        return (new MailMessage)
            ->subject('Pagamento atrasado - TreinaHub')
            ->greeting("Olá, {$notifiable->name}!")
            ->line('Seu pagamento está atrasado.')
            ->line('Você tem 7 dias de carência antes do bloqueio do acesso.')
            ->action('Atualizar Pagamento', url('/subscription/plans'))
            ->line('Entre em contato se precisar de ajuda.');
    }
}
