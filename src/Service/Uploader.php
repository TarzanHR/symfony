<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class Uploader
{
    private string $uploadDirectory;
    private SluggerInterface $slugger;

    public function __construct(string $uploadDirectory, SluggerInterface $slugger)
    {
        $this->uploadDirectory = $uploadDirectory;
        $this->slugger = $slugger;
    }

    // Permet d'enregistrer l'image
    public function uploadFile(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->uploadDirectory, $newFilename);
        } catch (FileException $e) {
            throw new FileException('Une erreur est survenue lors du téléchargement du fichier.');
        }

        return $newFilename;
    }

    // Remplace l'ancienne image pra la nouvelle
    public function replaceFile(?string $oldFilename, UploadedFile $newFile): string
    {
        if ($oldFilename) {
            $this->deleteFile($oldFilename);
        }

        return $this->uploadFile($newFile);
    }

    // Supprime l'image du dossier 'public/uploads/images/'
    public function deleteFile(string $filename): void
    {
        $filePath = $this->uploadDirectory . '/' . $filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
