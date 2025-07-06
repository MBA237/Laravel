<?php

namespace App\Http\Controllers\Usecases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/usecases/upload",
     *     summary="Télécharger un fichier",
     *     tags={"Fichiers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="file", type="string", format="binary", description="Fichier à télécharger")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="URL publique du fichier téléchargé",
     *         @OA\JsonContent(
     *             @OA\Property(property="url", type="string", example="http://example.com/images/project/file.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation échouée"
     *     )
     * )
     */
    public function uploadFile(Request $request)
    {
        // Valider la requête
        $request->validate([
            'file' => 'required|file',
        ]);

        // Récupérer le fichier
        $file = $request->file('file');

        // Définir le chemin de destination dans public/images/project
        $destinationPath = public_path('images/project');

        // Créer le dossier s’il n’existe pas
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Générer un nom unique pour éviter les collisions
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Déplacer le fichier dans public/images/project
        $file->move($destinationPath, $fileName);

        // Construire l’URL publique
        $publicUrl = url('images/project/' . $fileName);

        return response()->json(['url' => $publicUrl], 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * @OA\Delete(
     *     path="/api/usecases/upload/delete",
     *     summary="Supprimer un fichier",
     *     tags={"Fichiers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="filename", type="string", description="Nom du fichier à supprimer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Fichier supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="File deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Fichier non trouvé"
     *     )
     * )
     */
    public function deleteFile(Request $request)
    {
        // Valider la requête
        $request->validate([
            'filename' => 'required|string',
        ]);

        // Chemin complet du fichier à supprimer
        $filePath = public_path('images/project/' . $request->input('filename'));

        // Supprimer le fichier s’il existe
        if (file_exists($filePath)) {
            unlink($filePath);
            return response()->json(['message' => 'File deleted successfully.']);
        } else {
            return response()->json(['message' => 'File not found.'], 404);
        }
    }
}
