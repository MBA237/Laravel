<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,Searchable;

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom_user',
        'email',
        'photo',
        'tbl_filiere_id',
        'password',
        'surname',
        
    ];

    public function filiere()
    {
        return $this->belongsTo(TblFiliere::class, 'tbl_filiere_id');
    }

    public function projets()
    {
        // Récupère tous les projets liés à l'utilisateur via ses collaborateurs (many-to-many via la table pivot)
        return TblProjet::whereHas('collaborateurs', function ($query) {
            $query->whereIn('tbl_collaborateurs.id', $this->collaborateurs()->pluck('tbl_collaborateurs.id'));
        });
    }

    public function documents()
    {
        return $this->hasMany(TblDocument::class);
    }

    public function collaborateurs()
    {
        return $this->belongsToMany(TblCollaborateur::class, 'collaborateur_utilisateur');
    }

    public function superviseurs()
    {
        return $this->belongsToMany(TblSuperviseur::class, 'superviseur_utilisateur');
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();

        return [
            'nom_user' => $array['nom_user'],
        ];
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
