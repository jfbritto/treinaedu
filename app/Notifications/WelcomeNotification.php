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
        return (new MailMessage)
            ->subject('Bem-vindo ao TreinaEdu!')
            ->greeting("Olá, {$notifiable->name}!")
            ->line('Sua empresa foi cadastrada com sucesso no TreinaEdu.')
            ->line('Você tem 7 dias de teste gratuito para explorar todas as funcionalidades.')
            ->action('Acessar Dashboard', url('/dashboard'))
            ->line('Comece criando seus treinamentos e cadastrando sua equipe.');
    }
}
