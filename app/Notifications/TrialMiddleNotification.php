<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialMiddleNotification extends Notification
{
    public function __construct(private int $daysLeft) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $firstName = explode(' ', $notifiable->name)[0];

        return (new MailMessage)
            ->subject("Restam {$this->daysLeft} dias no seu teste - TreinaEdu")
            ->greeting("Olá, {$firstName}!")
            ->line("Você já está usando o TreinaEdu há alguns dias. Restam **{$this->daysLeft} dias** no seu período de teste gratuito.")
            ->line('Aproveite para explorar tudo que a plataforma oferece:')
            ->line('- **Crie treinamentos** com vídeos e módulos')
            ->line('- **Gere quizzes com IA** para avaliar sua equipe')
            ->line('- **Monte trilhas** de aprendizagem completas')
            ->line('- **Emita certificados** digitais verificáveis')
            ->action('Acessar o TreinaEdu', url('/dashboard'))
            ->line('Quando estiver pronto, escolha o plano ideal para sua empresa.')
            ->salutation('Equipe TreinaEdu');
    }
}
