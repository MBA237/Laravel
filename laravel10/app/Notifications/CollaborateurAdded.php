<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CollaborateurAdded extends Notification
{
    use Queueable;

    protected $nom;
    protected $projet;

    public function __construct($nom, $projet)
    {
        $this->nom = $nom;
        $this->projet = $projet;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Vous avez été ajouté comme collaborateur')
            ->greeting('Bonjour ' . $this->nom . ',')
            ->line('Vous avez été ajouté comme collaborateur au projet : ' . $this->projet->titre_projet)
            ->action('Voir le projet', url('/projects/' . $this->projet->id))
            ->line('Merci de votre participation.')
            ->salutation('Cordialement, L’équipe');
    }

    public function toArray($notifiable)
    {
        return [
            'project_id' => $this->projet->id,
            'project_title' => $this->projet->titre_projet,
            'message' => 'Vous avez été ajouté comme collaborateur au projet : ' . $this->projet->titre_projet,
            'url' => url('/projects/' . $this->projet->id)
        ];
    }
}
