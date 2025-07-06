<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class TblProjet extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'titre_projet',
        'descript_projet',
        'superviseurs',
        'tbl_niveau_id',
        'tbl_categorie_id',
        'user_id',
        'views',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function niveau()
    {
        return $this->belongsTo(TblNiveau::class, 'tbl_niveau_id');
    }

    public function categorie()
    {
        return $this->belongsTo(TblCategorie::class, 'tbl_categorie_id');
    }

    public function documents()
    {
        return $this->hasMany(TblDocument::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'collaborateur_projets');
    }

    public function superviseurs()
    {
        return $this->belongsToMany(
            TblSuperviseur::class,
            'projet_superviseur',   // nom exact de la table pivot
            'projet_id',            // clé étrangère locale dans la pivot
            'superviseur_id'        // clé étrangère liée
        );
    }

    public function collaborateurs()
    {
        // Relation many-to-many avec les collaborateurs via la table pivot tbl_collaborateur_projets
        return $this->belongsToMany(
            TblCollaborateur::class,
            'tbl_collaborateur_projets',
            'tbl_projet_id',
            'tbl_collaborateur_id'
        );
    }

    public function toSearchableArray()
    {
        return [
            'titre_projet' => $this->titre_projet,
            'descript_projet' => $this->descript_projet,
        ];
    }
    
}
        // Envoi de la notification au superviseur
      
