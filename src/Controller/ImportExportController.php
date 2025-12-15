<?php

namespace App\Controller;

use App\Manager\ImportExportManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportExportController extends AbstractController
{
    public function __construct(
        private readonly ImportExportManager $importExportManager,
    )
    {
    }

    #[Route('/admin/import-terms', name: 'admin_import_terms', methods: ['GET', 'POST'])]
    public function importTerms(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $file = $request->files->get('excel_file');
            
            if ($file) {
                try {
                    // Extraire les données du fichier Excel
                    $extractedData = $this->importExportManager->importTerms($file);
                    
                    // Sauvegarder les données en base de données
                    $stats = $this->importExportManager->saveImportedData(
                        $extractedData['data'],
                        $this->getUser()
                    );
                    
                    // Retourner les statistiques en JSON
                    $message = '';
                    if ($stats['success'] > 0) {
                        $message .= $stats['success'] . ' nouveau(x) terme(s) créé(s)';
                    }
                    if ($stats['updated'] > 0) {
                        $message .= ($message ? ', ' : '') . $stats['updated'] . ' terme(s) mis à jour';
                    }
                    if ($stats['errors'] > 0) {
                        $message .= ($message ? ', ' : '') . $stats['errors'] . ' erreur(s)';
                    }
                    
                    return $this->json([
                        'success' => true,
                        'stats' => $stats,
                        'message' => $message
                    ]);
                } catch (\Exception $e) {
                    return $this->json([
                        'success' => false,
                        'error' => $e->getMessage()
                    ], 400);
                }
            }
            
            return $this->json([
                'success' => false,
                'error' => 'Aucun fichier uploadé'
            ], 400);
        }
        
        // Formulaire d'upload
        return $this->render('admin/import_export/import_terms.html.twig');
    }
}
