<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCancelledNotification extends Notification
{
    public function __construct(private ?string $accessUntil = null) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $firstName = explode(' ', $notifiable->name)[0];

        $mail = (new MailMessage)
            ->subject('Assinatura cancelada - TreinaEdu')
            ->greeting("Olá, {$firstName}.");

        if ($this->accessUntil) {
            $mail->line('Sua assinatura do TreinaEdu foi **cancelada** conforme solicitado.')
                 ->line("Você e sua equipe **continuam com acesso total até {$this->accessUntil}**. Após essa data, a plataforma será bloqueada.")
                 ->line('Os dados da sua empresa (treinamentos, certificados, progresso dos colaboradores) serão **preservados por 30 dias** após o bloqueio.');
        } else {
            $mail->line('Sua assinatura do TreinaEdu foi **cancelada**.')
                 ->line('O acesso à plataforma foi bloqueado. Os dados da sua empresa serão mantidos por 30 dias.');
        }

        $mail->line('Se mudou de ideia, é fácil voltar — basta assinar um plano e tudo estará exatamente como você deixou:')
             ->action('Reativar Assinatura', url('/subscription/plans'))
             ->line('Sentiremos sua falta!')
             ->salutation('Equipe TreinaEdu');

        return $mail;
    }
}
