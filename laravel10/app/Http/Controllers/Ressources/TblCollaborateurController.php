<?php

namespace App\Http\Controllers\Ressources;

use App\Http\Controllers\Controller;
use App\Models\TblCollaborateur;
use App\Models\TblProjet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\CollaborateurAdded;

class TblCollaborateurController extends Controller
{
    public function index()
    {
        $collaborateurs = TblCollaborateur::all();
        return response()->json($collaborateurs);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_collab'=>'required|max:255',
            'email_collab'=>'required|email|max:255',
            'user_id' => 'required|exists:users,id',
            'tbl_projet_id' => 'required|exists:tbl_projets,id',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // On cherche ou crée le collaborateur (même email/noms pour plusieurs projets)
        $collaborateur = TblCollaborateur::firstOrCreate(
            [
                'email_collab' => $request->email_collab,
                'nom_collab' => $request->nom_collab
            ],
            [
                'user_id' => $request->user_id
            ]
        );

        // On crée le lien dans la table pivot projet-collaborateur
        \App\Models\TblCollaborateurProjet::firstOrCreate([
            'tbl_projet_id' => $request->tbl_projet_id,
            'tbl_collaborateur_id' => $collaborateur->id
        ]);

        // Récupérer l'utilisateur ajouté comme collaborateur
        $user = \App\Models\User::find($request->user_id);
        $projet = TblProjet::find($request->tbl_projet_id);

        // Associer le collaborateur à l'utilisateur (table pivot collaborateur_utilisateur)
        if ($user && $collaborateur) {
            $user->collaborateurs()->syncWithoutDetaching([$collaborateur->id]);
        }
        // (Notification supprimée ici pour éviter les doublons, elle sera envoyée via l'API dédiée si besoin)

        return response()->json($collaborateur, 201);
    }

    public function show(string $id)
    {
        $collaborateur = TblCollaborateur::findOrFail($id);
        return response()->json($collaborateur);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nom_collab' => 'required|max:255',
            'email_collab' => 'required|email|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $collaborateur = TblCollaborateur::findOrFail($id);
        $collaborateur->nom_collab = $request->nom_collab;
        $collaborateur->email_collab = $request->email_collab;
        $collaborateur->user_id = $request->user_id;
        $collaborateur->save();

        // L'association projet-collaborateur reste inchangée (table pivot)
        return response()->json($collaborateur);
    }

    public function addToProject(Request $request, $projectId)
    {

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Vérifie que le projet existe
        $projet = TblProjet::find($projectId);
        if (!$projet) {
            return response()->json(['message' => 'Projet non trouvé'], 404);
        }

        // Vérifie que l'utilisateur courant est le créateur du projet
        $userId = auth()->id();
        if ($projet->user_id != $userId) {
            return response()->json(['message' => 'Action non autorisée'], 403);
        }

        // Crée ou récupère le collaborateur
        $collab = TblCollaborateur::firstOrCreate(
            ['email_collab' => $request->email],
            ['nom_collab' => $request->nom]
        );

        // Ajoute le lien avec le projet (many-to-many)
        $collab->projets()->syncWithoutDetaching([$projectId]);

        // Envoie un email
        Mail::to($collab->email_collab)->send(new CollaborateurAdded($collab));

        return response()->json([
            'message' => 'Collaborateur ajouté au projet et email envoyé !',
            'collaborateur' => $collab
        ], 201);
    }

    public function destroy(string $id)
    {
        $deleted = TblCollaborateur::destroy($id);
        if (!$deleted) {
            return response()->json(['message' => 'Collaborateur non trouvé'], 404);
        }
        return response()->noContent();
    }
}
