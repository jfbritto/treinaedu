<?php
namespace App\Notifications;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
class PaymentConfirmedNotification extends Notification {
    public function __construct(private float $amount) {}
    public function via($notifiable): array { return ['mail']; }
    public function toMail($notifiable): MailMessage {
        return (new MailMessage)
            ->subject('Pagamento confirmado - TreinaHub')
            ->greeting("Olá, {$notifiable->name}!")
            ->line('Seu pagamento de R$ ' . number_format($this->amount, 2, ',', '.') . ' foi confirmado.')
            ->line('Sua assinatura está ativa.')
            ->action('Acessar Dashboard', url('/dashboard'));
    }
}
