<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class TblSuperviseur extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom_sup',
        'email_sup',
    ];

    public function projets()
    {
        return $this->belongsToMany(
            TblProjet::class,
            'projet_superviseur',
            'superviseur_id',
            'projet_id'
        );
    }
}
