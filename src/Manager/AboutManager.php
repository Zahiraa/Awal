<?php

namespace App\Manager;

use App\Entity\About;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;

class AboutManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FileUploadService $fileUploadService
    ) {}

    public function saveAbout(About $about, $image = null): About
    {
        if ($image) {
            $this->uploadImage($about, $image);
        }

        $this->entityManager->persist($about);
        $this->entityManager->flush();

        return $about;
    }

    public function editAbout(About $about, $image = null): About
    {
        if ($image) {
            $this->uploadImage($about, $image);
        }

        $this->entityManager->flush();

        return $about;
    }

    private function uploadImage(About $about, $image): void
    {
        if ($about->getImage()) {
            $oldImage = $this->fileUploadService->delete($about->getImage()->getName());
            if ($oldImage) {
                $this->entityManager->remove($oldImage);
            }
        }

        $file = $this->fileUploadService->upload($image);
        $about->setImage($file);
    }
}