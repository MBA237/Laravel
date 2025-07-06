<?php

namespace App\Http\Controllers\Usecases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller {
    // Envoie une notification à un collaborateur ou à tous les collaborateurs d'un projet
    // (Définition unique, doublon supprimé)
    public function sendProjectNotification(Request $request)
    {
        $request->validate([
            'projectId' => 'required|integer|exists:tbl_projets,id',
            'collaboratorEmail' => 'nullable|email',
            'message' => 'required|string',
        ]);

        $projet = \App\Models\TblProjet::find($request->projectId);
        if (!$projet) {
            return response()->json(['message' => 'Projet non trouvé'], 404);
        }

        $notified = 0;
        // Si un email de collaborateur est fourni, notifier ce collaborateur uniquement
        if ($request->filled('collaboratorEmail')) {
            $collab = \App\Models\TblCollaborateur::where('email_collab', $request->collaboratorEmail)->first();
            if ($collab && $collab->user_id) {
                $user = \App\Models\User::find($collab->user_id);
                if ($user) {
                    $user->notify(new \App\Notifications\CollaborateurAdded($user->nom_user, $projet));
                    $notified++;
                }
            }
        } else {
            // Sinon, notifier tous les collaborateurs liés au projet
            foreach ($projet->collaborateurs as $collab) {
                if ($collab->user_id) {
                    $user = \App\Models\User::find($collab->user_id);
                    if ($user) {
                        $user->notify(new \App\Notifications\CollaborateurAdded($user->nom_user, $projet));
                        $notified++;
                    }
                }
            }
        }

        // Notifier aussi le créateur du projet si besoin (optionnel)
        // if ($projet->user) {
        //     $projet->user->notify(new \App\Notifications\CollaborateurAdded($projet->user->nom_user, $projet));
        // }

        // Ne pas notifier le créateur du projet comme collaborateur
        // (il ne doit pas recevoir la notification "ajouté comme collaborateur")
        // On considère que $projet->user_id est l'id du créateur
        // Si le créateur est aussi collaborateur, il recevra la notification via la boucle ci-dessus

        // Si vous souhaitez notifier le créateur d'un autre type de notification, ajoutez-le ici

        return response()->json(['message' => "Notification envoyée à $notified collaborateur(s) (y compris le créateur) !"]);
    }

    // Récupérer toutes les notifications de l'utilisateur connecté
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();
        return response()->json($notifications);
    }

    // Marquer une notification comme lue
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notification marquée comme lue.']);
        }
        return response()->json(['message' => 'Notification non trouvée.'], 404);
    }

    // Marquer toutes les notifications comme lues
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();
        return response()->json(['message' => 'Toutes les notifications ont été marquées comme lues.']);
    }

    // (Définition unique, doublon supprimé)
}
