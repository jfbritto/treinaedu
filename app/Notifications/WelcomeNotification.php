<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $firstName = explode(' ', $notifiable->name)[0];

        return (new MailMessage)
            ->subject('Bem-vindo ao TreinaEdu!')
            ->greeting("Olá, {$firstName}!")
            ->line('Sua empresa foi cadastrada com sucesso no **TreinaEdu** — a plataforma completa de treinamentos corporativos.')
            ->line('Você tem **7 dias de teste gratuito** para explorar todas as funcionalidades:')
            ->line('- Criar treinamentos com vídeos, textos e documentos')
            ->line('- Organizar módulos e aulas sequenciais')
            ->line('- Aplicar quizzes com notas de aprovação')
            ->line('- Emitir certificados digitais verificáveis')
            ->action('Acessar o Dashboard', url('/dashboard'))
            ->line('Precisa de ajuda? Responda este e-mail que teremos prazer em ajudar.')
            ->salutation('Equipe TreinaEdu');
    }
}
