<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\TblCollaborateur;

class CollaborateurAdded extends Mailable
{
    use Queueable, SerializesModels;

    public $collab;

    public function __construct(TblCollaborateur $collab)
    {
        $this->collab = $collab;
    }

    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
                    ->subject('Ajout en tant que collaborateur')
                    ->markdown('emails.collaborateur');
    }
}
