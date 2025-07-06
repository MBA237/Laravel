<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileUploadService
{
    /**
     * Upload un fichier dans le dossier public/images/project ou autre.
     *
     * @param UploadedFile $file
     * @param string $relativePath - Exemple : 'images/project'
     * @return string URL publique
     */
    public function uploadFile(UploadedFile $file, string $relativePath): string
    {
        $destinationPath = public_path($relativePath);

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $uniqueFileName = $originalName . '_' . time() . '_' . uniqid() . '.' . $extension;

        $file->move($destinationPath, $uniqueFileName);

        // Retourner l'URL publique
        return url($relativePath . '/' . $uniqueFileName);
    }

    /**
     * Supprime un fichier à partir de son URL publique complète ou chemin relatif
     *
     * @param string $publicUrl
     * @return bool
     */
    public function deleteFile(string $publicUrl): bool
    {
        // Convertir l'URL en chemin absolu
        $relativePath = str_replace(url('/'), '', $publicUrl);
        $fullPath = public_path($relativePath);

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }
}
