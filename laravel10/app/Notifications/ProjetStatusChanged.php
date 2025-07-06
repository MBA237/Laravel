<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ProjetStatusChanged extends Notification
{
    use Queueable;

    protected $projet;
    protected $newStatus;

    public function __construct($projet, $newStatus)
    {
        $this->projet = $projet;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $nom = property_exists($notifiable, 'nom_user') ? $notifiable->nom_user : (property_exists($notifiable, 'name') ? $notifiable->name : '');
        return (new MailMessage)
            ->subject('Changement de statut du projet')
            ->greeting('Bonjour ' . $nom . ',')
            ->line('Le statut du projet "' . $this->projet->titre_projet . '" a changé.')
            ->line('Nouveau statut : ' . $this->newStatus)
            ->action('Voir le projet', url('/projects/' . $this->projet->id))
            ->salutation('Cordialement, L’équipe');
    }

    public function toArray($notifiable)
    {
        return [
            'project_id' => $this->projet->id,
            'project_title' => $this->projet->titre_projet,
            'new_status' => $this->newStatus,
            'message' => 'Le statut du projet a changé : ' . $this->newStatus,
            'url' => url('/projects/' . $this->projet->id)
        ];
    }
}
