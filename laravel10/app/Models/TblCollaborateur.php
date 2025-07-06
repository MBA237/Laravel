<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

/**
 * @OA\Schema(
 *     schema="TblCollaborateur",
 *     type="object",
 *     title="TblCollaborateur",
 *     required={"nom_collab", "email_collab"},
 *     @OA\Property(property="id", type="integer", format="int64", description="Identifiant unique du collaborateur"),
 *     @OA\Property(property="nom_collab", type="string", description="Nom du collaborateur"),
 *     @OA\Property(property="email_collab", type="string", format="email", description="Email du collaborateur"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class TblCollaborateur extends Model
{
    use HasFactory, Searchable;

    protected $table = 'tbl_collaborateurs';

    protected $fillable = [
        'nom_collab',
        'email_collab',
        'user_id',
        // 'tbl_projet_id', // Supprimé car la relation projet est maintenant many-to-many via la table pivot
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation many-to-many avec les projets via la table pivot tbl_collaborateur_projets
    public function projets()
    {
        return $this->belongsToMany(
            TblProjet::class,
            'tbl_collaborateur_projets',
            'tbl_collaborateur_id',
            'tbl_projet_id'
        );
    }

    public function toSearchableArray()
    {
        return [
            'nom_collab' => $this->nom_collab,
            'email_collab' => $this->email_collab,
        ];
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
