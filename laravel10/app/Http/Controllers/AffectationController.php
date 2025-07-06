<?php

namespace App\Http\Controllers;

use App\Models\TblProjet;
use App\Models\TblSuperviseur;
use App\Notifications\SuperviseurAffecteAuProjet;
use Illuminate\Http\Request;

class AffectationController extends Controller
{
    public function assignerSuperviseur(Request $request)
    {
        $request->validate([
            'projet_id' => 'required|exists:tbl_projets,id',
            'superviseur_id' => 'required|exists:tbl_superviseurs,id',
        ]);

        $projet = TblProjet::findOrFail($request->projet_id);
        $superviseur = TblSuperviseur::findOrFail($request->superviseur_id);

        // Vérifie s'il n'est pas déjà affecté
        if ($projet->superviseurs()->where('tbl_superviseurs.id', $superviseur->id)->exists()) {
            return response()->json(['message' => 'Ce superviseur est déjà affecté à ce projet.'], 409);
        }

        // Affectation
        $projet->superviseurs()->attach($superviseur->id);

        // Notification
        $superviseur->notify(new SuperviseurAffecteAuProjet($projet));

        return response()->json(['message' => 'Superviseur affecté et notifié avec succès.']);
    }
}
