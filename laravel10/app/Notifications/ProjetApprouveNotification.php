<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ProjetApprouveNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $projet;

    /**
     * Create a new notification instance.
     */
    public function __construct($projet)
    {
        $this->projet = $projet;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Projet approuvé')
            ->view('emails.projet_approuve', [
                'projet' => $this->projet
            ]);
    }

    /**
     * Get the array representation of the notification for database.
     */
    public function toArray($notifiable)
    {
        return [
            'projet_id' => $this->projet->id,
            'titre_projet' => $this->projet->titre_projet,
            'message' => 'Le projet "' . $this->projet->titre_projet . '" auquel vous collaborez a été approuvé.',
            'url' => url('/projects/' . $this->projet->id),
        ];
    }
}
