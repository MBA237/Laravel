<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SuperviseurAddedNotification extends Notification
{
    use Queueable;

    protected $projectName;
    protected $projectId;

    public function __construct(string $projectName, int $projectId)
    {
        $this->projectName = $projectName;
        $this->projectId = $projectId;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Vous avez été ajouté comme superviseur')
            ->view('emails.superviseur-added', [
                'superviseur' => $notifiable,
                'projectName' => $this->projectName,
                'projectId' => $this->projectId,
            ]);
    }
}
