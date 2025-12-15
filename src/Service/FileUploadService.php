<?php

namespace App\Service;

use App\Entity\File;
use App\Repository\FileRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploadService
{
    public function __construct(private $uploadDirectory, private SluggerInterface $slugger, private FileRepository $fileRepository)
    {
    }

    public function upload(UploadedFile $uploadedFile): File
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

        try {
            $uploadedFile->move($this->uploadDirectory, $fileName);
        } catch (FileException $e) {
            throw new \Exception('Erreur lors du téléchargement du fichier: ' . $e->getMessage());
        }

        $filePath = $this->uploadDirectory . '/' . $fileName;
        $fileInfo = new \Symfony\Component\HttpFoundation\File\File($filePath);

        $file = new File();
        $file->setName($fileName);
        $file->setSize((string) $fileInfo->getSize());
        $file->setExtension($fileInfo->guessExtension());

        return $file;
    }


    public function delete(string $filename)
    {
        $filePath = $this->uploadDirectory . '/' . $filename;
        if (file_exists($filePath)) {
             unlink($filePath);
            return  $this->fileRepository->findOneBy(['name' => $filename]) ?? null;
        }
    }

}