<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\TblProjet;

class SuperviseurAffecteAuProjet extends Notification
{
    use Queueable;

    public $projet;

    public function __construct(TblProjet $projet)
    {
        $this->projet = $projet;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Affectation à un projet')
            ->greeting('Bonjour ' . $notifiable->nom_sup)
            ->line('Vous avez été affecté au projet "' . $this->projet->titre_projet . '".')
            ->action('Voir le projet', url('/projets/' . $this->projet->id))
            ->line('Merci pour votre collaboration.');
    }
}

