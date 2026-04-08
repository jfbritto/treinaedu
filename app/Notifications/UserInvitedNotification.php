<?php

namespace App\Notifications;

use App\Models\Company;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitedNotification extends Notification
{
    public function __construct(
        private string $token,
        private User $invitedBy,
        private Company $company,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $firstName = explode(' ', $notifiable->name)[0];
        $inviterFirstName = explode(' ', $this->invitedBy->name)[0];
        $roleLabel = match ($notifiable->role) {
            'admin'      => 'Administrador',
            'instructor' => 'Instrutor',
            'employee'   => 'Colaborador',
            default      => 'Usuário',
        };

        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject("{$inviterFirstName} convidou você para o TreinaEdu")
            ->greeting("Olá, {$firstName}!")
            ->line("**{$this->invitedBy->name}** convidou você para participar do **TreinaEdu** na empresa **{$this->company->name}**.")
            ->line("Você foi cadastrado(a) como **{$roleLabel}** e poderá acessar treinamentos, fazer quizzes e gerar certificados.")
            ->line('Para começar, defina sua senha pessoal clicando no botão abaixo:')
            ->action('Definir minha senha', $url)
            ->line('Este link é válido por **7 dias**. Após definir sua senha, você poderá acessar normalmente com seu e-mail e a senha escolhida.')
            ->line('Se você não esperava este convite, pode ignorar este e-mail com segurança.')
            ->salutation('Equipe TreinaEdu');
    }
}
