<?php

namespace App\Http\Controllers\Usecases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\FileUploadService;

class ProfileController extends Controller
{
    private $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    // GET /user
    public function getUserProfile()
    {
        $user = Auth::user();
        return response()->json($user, 200);
    }

    // PUT /user/update-name
    public function updateName(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nom_user' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
        ]);

        $user->nom_user = $request->input('nom_user');
        if ($request->filled('surname')) {
            $user->surname = $request->input('surname');
        }

        $user->save();

        return response()->json(['message' => 'Nom mis à jour avec succès.', 'user' => $user], 200);
    }

    // PUT /user/email
    public function updateEmail(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->email = $request->input('email');
        $user->save();

        return response()->json(['message' => 'Email mis à jour avec succès.', 'user' => $user], 200);
    }

    // PUT /user/update-password
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'oldPassword' => 'required|string',
            'newPassword' => 'required|string|min:4',
        ]);

        // Vérifier l'ancien mot de passe
        if (!Hash::check($request->input('oldPassword'), $user->password)) {
            return response()->json(['message' => 'Ancien mot de passe incorrect.'], 422);
        }

        $user->password = Hash::make($request->input('newPassword'));
        $user->save();

        return response()->json(['message' => 'Mot de passe mis à jour avec succès.'], 200);
    }

    // POST /user/photo
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Supprimer l'ancienne photo si elle existe
        if ($user->photo) {
            $oldPath = public_path($user->photo);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $file = $request->file('photo');

        // Générer un nom de fichier unique
        $filename = time() . '_' . $file->getClientOriginalName();

        // Déplacer le fichier dans public/images
        $file->move(public_path('images'), $filename);

        // Enregistrer chemin relatif dans la base (ex: images/nomfichier.jpg)
        $user->photo = 'images/' . $filename;
        $user->save();

        // Retourner l'URL complète accessible publiquement
        return response()->json([
            'message' => 'Photo de profil mise à jour avec succès.',
            'photo' => url('images/' . $filename)  // ex: http://localhost:8000/images/nomfichier.jpg
        ], 200);
    }
}
