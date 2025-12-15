<?php

namespace App\Manager;

use App\Entity\Content;
use App\Entity\Terme;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportExportManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    
    public function importTerms(UploadedFile $file): array
    {
        // Charger le fichier Excel
        $spreadsheet = IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Récupérer les données
        $data = [];
        
        // Ligne 1: Catégories (chaque catégorie fait 3 colonnes)
        $categories = [];
        $columnIndex = 1; // Commence à 1 (colonne A)
        $categoryRow = 1;
        
        while (true) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
            $cellValue = $worksheet->getCell($columnLetter . $categoryRow)->getValue();
            
            if ($cellValue === null) {
                break;
            }
            
            $categories[] = [
                'name' => $cellValue,
                'startColumnIndex' => $columnIndex,
                'endColumnIndex' => $columnIndex + 2 // +2 car chaque catégorie fait 3 colonnes
            ];
            
            // Passer à la catégorie suivante (sauter 3 colonnes)
            $columnIndex += 3;
        }
        
        // Ligne 2: Langues (Français, Arabe, Darija)
        $languages = [];
        $languageRow = 2;
        foreach ($categories as $category) {
            $categoryLanguages = [];
            for ($i = 0; $i < 3; $i++) {
                $colIndex = $category['startColumnIndex'] + $i;
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $langName = $worksheet->getCell($colLetter . $languageRow)->getValue();
                if ($langName) {
                    $categoryLanguages[] = [
                        'name' => $langName,
                        'columnIndex' => $colIndex,
                        'columnLetter' => $colLetter
                    ];
                }
            }
            $languages[$category['name']] = $categoryLanguages;
        }
        
        // Extraction des données à partir de la ligne 3
        $rowNumber = 3;
        $highestRow = $worksheet->getHighestRow();
        
        while ($rowNumber <= $highestRow) {
            $rowData = [];
            $hasData = false;
            
            foreach ($categories as $category) {
                $categoryData = [];
                
                // Récupérer les données pour chaque langue de cette catégorie
                foreach ($languages[$category['name']] as $language) {
                    $cellValue = $worksheet->getCell($language['columnLetter'] . $rowNumber)->getValue();
                    if ($cellValue !== null) {
                        $hasData = true;
                    }
                    $categoryData[$language['name']] = $cellValue;
                }
                
                $rowData[$category['name']] = $categoryData;
            }
            
            // N'ajouter que les lignes qui contiennent des données
            if ($hasData) {
                $data[] = $rowData;
            }
            
            $rowNumber++;
        }
        
        return [
            'data' => $data,
        ];
    }
    
    /**
     * Sauvegarde les données extraites en base de données
     * 
     * @param array $data Données extraites du fichier Excel
     * @param User $user Utilisateur qui effectue l'import
     * @return array Statistiques de l'import (succès, erreurs, mises à jour)
     */
    public function saveImportedData(array $data, User $user): array
    {
        $stats = [
            'success' => 0,
            'updated' => 0,
            'errors' => 0,
            'errorMessages' => []
        ];
        
        // Mapping des noms de catégories Excel vers les noms de méthodes Content
        $categoryMapping = [
            'nom' => 'Titre',
            'titre' => 'Titre',
            'description' => 'Description',
            'synonyme' => 'Synonyme',
            'domaine_applications' => 'DomaineApplications',
            'domaine d\'applications' => 'DomaineApplications',
            'categorie' => 'Categorie',
            'catégorie' => 'Categorie',
            'source' => 'Source',
            'categorie_grammaticale' => 'CategorieGrammaticale',
            'catégorie grammaticale' => 'CategorieGrammaticale',
            'relation_terminologique' => 'RelationTerminologique',
            'relation terminologique' => 'RelationTerminologique',
            'equivalent_anglais' => 'EquivalentAnglais',
            'équivalent anglais' => 'EquivalentAnglais',
            'equivalent_espagnol' => 'EquivalentEspagnol',
            'équivalent espagnol' => 'EquivalentEspagnol',
            'idiome' => 'Idiome',
            'usage_metaphorique' => 'UsageMetaphorique',
            'usage métaphorique' => 'UsageMetaphorique',
            'recit_vie' => 'RecitVie',
            'récit de vie' => 'RecitVie',
            'liens_hypertexte' => 'LiensHypertexte',
            'liens hypertexte' => 'LiensHypertexte',
        ];
        
        // Mapping des noms de langues
        $languageMapping = [
            'français' => 'Fr',
            'francais' => 'Fr',
            'french' => 'Fr',
            'arabe' => 'Ar',
            'arabic' => 'Ar',
            'darija' => 'Dr',
        ];
        
        foreach ($data as $index => $rowData) {
            try {
                // Extraire les titres pour rechercher un terme existant
                $titreFr = null;
                $titreAr = null;
                $titreDr = null;
                
                // Chercher les titres dans les données
                foreach ($rowData as $categoryName => $languages) {
                    $categoryKey = strtolower(trim($categoryName));
                    if (in_array($categoryKey, ['nom', 'titre'])) {
                        foreach ($languages as $languageName => $value) {
                            $languageKey = strtolower(trim($languageName));
                            if (isset($languageMapping[$languageKey]) && !empty($value)) {
                                $suffix = $languageMapping[$languageKey];
                                if ($suffix === 'Fr') $titreFr = $value;
                                if ($suffix === 'Ar') $titreAr = $value;
                                if ($suffix === 'Dr') $titreDr = $value;
                            }
                        }
                        break;
                    }
                }
                
                // Chercher un terme existant par titre
                $existingContent = null;
                $terme = null;
                $isUpdate = false;
                
                if ($titreFr || $titreAr || $titreDr) {
                    $qb = $this->entityManager->createQueryBuilder();
                    $qb->select('c')
                       ->from(Content::class, 'c')
                       ->where('1=0'); // Condition de base toujours fausse
                    
                    $conditions = [];
                    if ($titreFr) {
                        $conditions[] = 'c.titreFr = :titreFr';
                        $qb->setParameter('titreFr', $titreFr);
                    }
                    if ($titreAr) {
                        $conditions[] = 'c.titreAr = :titreAr';
                        $qb->setParameter('titreAr', $titreAr);
                    }
                    if ($titreDr) {
                        $conditions[] = 'c.titreDr = :titreDr';
                        $qb->setParameter('titreDr', $titreDr);
                    }
                    
                    if (!empty($conditions)) {
                        $qb->orWhere(implode(' OR ', $conditions));
                        $existingContent = $qb->getQuery()->getOneOrNullResult();
                    }
                }
                
                if ($existingContent) {
                    // Mise à jour d'un terme existant
                    $terme = $existingContent->getTerme();
                    $content = $existingContent;
                    $content->setUpdatedBy($user);
                    $isUpdate = true;
                } else {
                    // Créer un nouveau Terme
                    $terme = new Terme();
                    $terme->setStatut(Terme::STATUT_DRAFT);
                    $terme->setCreatedBy($user);
                    
                    // Créer un nouveau Content
                    $content = new Content();
                    $content->setTerme($terme);
                    $content->setCreatedBy($user);
                    $terme->addContent($content);
                }
                
                // Remplir/Mettre à jour les champs du Content selon les données Excel
                foreach ($rowData as $categoryName => $languages) {
                    // Normaliser le nom de la catégorie
                    $categoryKey = strtolower(trim($categoryName));
                    
                    if (!isset($categoryMapping[$categoryKey])) {
                        continue; // Ignorer les catégories non reconnues
                    }
                    
                    $methodPrefix = $categoryMapping[$categoryKey];
                    
                    // Remplir pour chaque langue
                    foreach ($languages as $languageName => $value) {
                        // Normaliser le nom de la langue
                        $languageKey = strtolower(trim($languageName));
                        
                        if (!isset($languageMapping[$languageKey])) {
                            continue; // Ignorer les langues non reconnues
                        }
                        
                        $languageSuffix = $languageMapping[$languageKey];
                        $methodName = 'set' . $methodPrefix . $languageSuffix;
                        
                        // Appeler la méthode setter si elle existe
                        // Si la valeur est vide et qu'on est en mode mise à jour, on ne modifie pas le champ
                        if (method_exists($content, $methodName)) {
                            if (!empty($value)) {
                                // Mettre à jour avec la nouvelle valeur
                                $content->$methodName($value);
                            } elseif (!$isUpdate) {
                                // Pour une création, on peut mettre null
                                $content->$methodName($value);
                            }
                            // Si vide et mise à jour, on ne fait rien (garde l'ancienne valeur)
                        }
                    }
                }
                
                // Persister en base de données
                if (!$isUpdate) {
                    $this->entityManager->persist($terme);
                    $this->entityManager->persist($content);
                    $stats['success']++;
                } else {
                    $stats['updated']++;
                }
                
            } catch (\Exception $e) {
                $stats['errors']++;
                $stats['errorMessages'][] = "Ligne " . ($index + 1) . ": " . $e->getMessage();
            }
        }
        
        // Flush tous les changements en une seule fois
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $stats['errors'] += $stats['success'];
            $stats['success'] = 0;
            $stats['errorMessages'][] = "Erreur lors de la sauvegarde: " . $e->getMessage();
        }
        
        return $stats;
    }
}
