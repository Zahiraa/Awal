<?php

namespace App\Entity;

use App\Repository\ContentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContentRepository::class)]
class Content
{
    use TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // TITLE
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titreFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titreAr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titreDr = null;

    // DESCRIPTION
    #[ORM\Column(length: 300, nullable: true)]
    private ?string $descriptionFr = null;

    #[ORM\Column(length: 300, nullable: true)]
    private ?string $descriptionAr = null;

    #[ORM\Column(length: 300, nullable: true)]
    private ?string $descriptionDr = null;

    // SYNOMYME
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $synonymeFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $synonymeAr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $synonymeDr = null;

    // DOMAINE_APPLICATIONS TYPE FIELD ARRAY
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $domaine_applicationsFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $domaine_applicationsAr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $domaine_applicationsDr = null;

    // CATEGORIE
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $categorieFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $categorieAr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $categorieDr = null;


    // SOURCE
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sourceFr =null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sourceAr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sourceDr = null;

    // CATEGORIE_GRAMMATICALE
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $categorie_grammaticaleFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $categorie_grammaticaleAr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $categorie_grammaticaleDr = null;

    // RELATION_TERMINILOGIQUE
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $relation_terminologiqueFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $relation_terminologiqueAr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $relation_terminologiqueDr = null;

    // EQUIVALENT_ANGLAIS
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $equivalent_anglaisFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $equivalent_anglaisAr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $equivalent_anglaisDr = null;

    // EQUIVALENT_ESPAGNOL
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $equivalent_espagnolFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $equivalent_espagnolAr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $equivalent_espagnolDr = null;

    // IDIOME
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $idiomeFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $idiomeAr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $idiomeDr = null;

    // USAGE_METAPHORIQUE
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $usage_metaphoriqueFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $usage_metaphoriqueAr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $usage_metaphoriqueDr = null;

    // RECIT_VIE
    #[ORM\Column(length: 500, nullable: true)]
    private ?string $recit_vieFr = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $recit_vieAr = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $recit_vieDr = null;

    // LIENS_HYPERTEXTE ARRAY FIELD
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $liens_hypertexteFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $liens_hypertexteAr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $liens_hypertexteDr = null;


    #[ORM\ManyToOne(inversedBy: 'contents')]
    private ?Terme $terme = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\ManyToOne]
    private ?User $updatedBy = null;

    #[ORM\ManyToOne(inversedBy: 'contents')]
    private ?Proposition $proposition = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitreFr(): ?string
    {
        return $this->titreFr;
    }

    public function setTitreFr(string $titreFr): static
    {
        $this->titreFr = $titreFr;

        return $this;
    }

    public function getDescriptionFr(): ?string
    {
        return $this->descriptionFr;
    }

    public function setDescriptionFr(string $descriptionFr): static
    {
        $this->descriptionFr = $descriptionFr;

        return $this;
    }

    public function getTitreAr(): ?string
    {
        return $this->titreAr;
    }

    public function setTitreAr(string $titreAr): static
    {
        $this->titreAr = $titreAr;

        return $this;
    }

    public function getDescriptionAr(): ?string
    {
        return $this->descriptionAr;
    }

    public function setDescriptionAr(string $descriptionAr): static
    {
        $this->descriptionAr = $descriptionAr;

        return $this;
    }

    public function getTitreDr(): ?string
    {
        return $this->titreDr;
    }

    public function setTitreDr(string $titreDr): static
    {
        $this->titreDr = $titreDr;

        return $this;
    }

    public function getDescriptionDr(): ?string
    {
        return $this->descriptionDr;
    }

    public function setDescriptionDr(?string $descriptionDr): static
    {
        $this->descriptionDr = $descriptionDr;

        return $this;
    }

    public function getTerme(): ?Terme
    {
        return $this->terme;
    }

    public function setTerme(?Terme $terme): static
    {
        $this->terme = $terme;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getProposition(): ?Proposition
    {
        return $this->proposition;
    }

    public function setProposition(?Proposition $proposition): static
    {
        $this->proposition = $proposition;

        return $this;
    }

    public function getSynonymeFr(): ?string
    {
        return $this->synonymeFr;
    }

    public function setSynonymeFr(?string $synonymeFr): void
    {
        $this->synonymeFr = $synonymeFr;
    }

    public function getSynonymeAr(): ?string
    {
        return $this->synonymeAr;
    }

    public function setSynonymeAr(?string $synonymeAr): void
    {
        $this->synonymeAr = $synonymeAr;
    }

    public function getSynonymeDr(): ?string
    {
        return $this->synonymeDr;
    }

    public function setSynonymeDr(?string $synonymeDr): void
    {
        $this->synonymeDr = $synonymeDr;
    }

    public function getDomaineApplicationsFr(): ?string
    {
        return $this->domaine_applicationsFr;
    }

    public function setDomaineApplicationsFr(?string $domaine_applicationsFr): void
    {
        $this->domaine_applicationsFr = $domaine_applicationsFr;
    }

    public function getDomaineApplicationsAr(): ?string
    {
        return $this->domaine_applicationsAr;
    }

    public function setDomaineApplicationsAr(?string $domaine_applicationsAr): void
    {
        $this->domaine_applicationsAr = $domaine_applicationsAr;
    }

    public function getDomaineApplicationsDr(): ?string
    {
        return $this->domaine_applicationsDr;
    }

    public function setDomaineApplicationsDr(?string $domaine_applicationsDr): void
    {
        $this->domaine_applicationsDr = $domaine_applicationsDr;
    }

    public function getCategorieFr(): ?string
    {
        return $this->categorieFr;
    }

    public function setCategorieFr(?string $categorieFr): void
    {
        $this->categorieFr = $categorieFr;
    }

    public function getCategorieAr(): ?string
    {
        return $this->categorieAr;
    }

    public function setCategorieAr(?string $categorieAr): void
    {
        $this->categorieAr = $categorieAr;
    }

    public function getCategorieDr(): ?string
    {
        return $this->categorieDr;
    }

    public function setCategorieDr(?string $categorieDr): void
    {
        $this->categorieDr = $categorieDr;
    }

    public function getSourceFr(): ?string
    {
        return $this->sourceFr;
    }

    public function setSourceFr(?string $sourceFr): void
    {
        $this->sourceFr = $sourceFr;
    }

    public function getSourceAr(): ?string
    {
        return $this->sourceAr;
    }

    public function setSourceAr(?string $sourceAr): void
    {
        $this->sourceAr = $sourceAr;
    }

    public function getSourceDr(): ?string
    {
        return $this->sourceDr;
    }

    public function setSourceDr(?string $sourceDr): void
    {
        $this->sourceDr = $sourceDr;
    }

    public function getCategorieGrammaticaleFr(): ?string
    {
        return $this->categorie_grammaticaleFr;
    }

    public function setCategorieGrammaticaleFr(?string $categorie_grammaticaleFr): void
    {
        $this->categorie_grammaticaleFr = $categorie_grammaticaleFr;
    }

    public function getCategorieGrammaticaleAr(): ?string
    {
        return $this->categorie_grammaticaleAr;
    }

    public function setCategorieGrammaticaleAr(?string $categorie_grammaticaleAr): void
    {
        $this->categorie_grammaticaleAr = $categorie_grammaticaleAr;
    }

    public function getCategorieGrammaticaleDr(): ?string
    {
        return $this->categorie_grammaticaleDr;
    }

    public function setCategorieGrammaticaleDr(?string $categorie_grammaticaleDr): void
    {
        $this->categorie_grammaticaleDr = $categorie_grammaticaleDr;
    }

    public function getRelationTerminologiqueFr(): ?string
    {
        return $this->relation_terminologiqueFr;
    }

    public function setRelationTerminologiqueFr(?string $relation_terminologiqueFr): void
    {
        $this->relation_terminologiqueFr = $relation_terminologiqueFr;
    }

    public function getRelationTerminologiqueAr(): ?string
    {
        return $this->relation_terminologiqueAr;
    }

    public function setRelationTerminologiqueAr(?string $relation_terminologiqueAr): void
    {
        $this->relation_terminologiqueAr = $relation_terminologiqueAr;
    }

    public function getRelationTerminologiqueDr(): ?string
    {
        return $this->relation_terminologiqueDr;
    }

    public function setRelationTerminologiqueDr(?string $relation_terminologiqueDr): void
    {
        $this->relation_terminologiqueDr = $relation_terminologiqueDr;
    }

    public function getEquivalentAnglaisFr(): ?string
    {
        return $this->equivalent_anglaisFr;
    }

    public function setEquivalentAnglaisFr(?string $equivalent_anglaisFr): void
    {
        $this->equivalent_anglaisFr = $equivalent_anglaisFr;
    }

    public function getEquivalentAnglaisAr(): ?string
    {
        return $this->equivalent_anglaisAr;
    }

    public function setEquivalentAnglaisAr(?string $equivalent_anglaisAr): void
    {
        $this->equivalent_anglaisAr = $equivalent_anglaisAr;
    }

    public function getEquivalentAnglaisDr(): ?string
    {
        return $this->equivalent_anglaisDr;
    }

    public function setEquivalentAnglaisDr(?string $equivalent_anglaisDr): void
    {
        $this->equivalent_anglaisDr = $equivalent_anglaisDr;
    }

    public function getEquivalentEspagnolFr(): ?string
    {
        return $this->equivalent_espagnolFr;
    }

    public function setEquivalentEspagnolFr(?string $equivalent_espagnolFr): void
    {
        $this->equivalent_espagnolFr = $equivalent_espagnolFr;
    }

    public function getEquivalentEspagnolAr(): ?string
    {
        return $this->equivalent_espagnolAr;
    }

    public function setEquivalentEspagnolAr(?string $equivalent_espagnolAr): void
    {
        $this->equivalent_espagnolAr = $equivalent_espagnolAr;
    }

    public function getEquivalentEspagnolDr(): ?string
    {
        return $this->equivalent_espagnolDr;
    }

    public function setEquivalentEspagnolDr(?string $equivalent_espagnolDr): void
    {
        $this->equivalent_espagnolDr = $equivalent_espagnolDr;
    }

    public function getIdiomeFr(): ?string
    {
        return $this->idiomeFr;
    }

    public function setIdiomeFr(?string $idiomeFr): void
    {
        $this->idiomeFr = $idiomeFr;
    }

    public function getIdiomeAr(): ?string
    {
        return $this->idiomeAr;
    }

    public function setIdiomeAr(?string $idiomeAr): void
    {
        $this->idiomeAr = $idiomeAr;
    }

    public function getIdiomeDr(): ?string
    {
        return $this->idiomeDr;
    }

    public function setIdiomeDr(?string $idiomeDr): void
    {
        $this->idiomeDr = $idiomeDr;
    }

    public function getUsageMetaphoriqueFr(): ?string
    {
        return $this->usage_metaphoriqueFr;
    }

    public function setUsageMetaphoriqueFr(?string $usage_metaphoriqueFr): void
    {
        $this->usage_metaphoriqueFr = $usage_metaphoriqueFr;
    }

    public function getUsageMetaphoriqueAr(): ?string
    {
        return $this->usage_metaphoriqueAr;
    }

    public function setUsageMetaphoriqueAr(?string $usage_metaphoriqueAr): void
    {
        $this->usage_metaphoriqueAr = $usage_metaphoriqueAr;
    }

    public function getUsageMetaphoriqueDr(): ?string
    {
        return $this->usage_metaphoriqueDr;
    }

    public function setUsageMetaphoriqueDr(?string $usage_metaphoriqueDr): void
    {
        $this->usage_metaphoriqueDr = $usage_metaphoriqueDr;
    }

    public function getRecitVieFr(): ?string
    {
        return $this->recit_vieFr;
    }

    public function setRecitVieFr(?string $recit_vieFr): void
    {
        $this->recit_vieFr = $recit_vieFr;
    }

    public function getRecitVieAr(): ?string
    {
        return $this->recit_vieAr;
    }

    public function setRecitVieAr(?string $recit_vieAr): void
    {
        $this->recit_vieAr = $recit_vieAr;
    }

    public function getRecitVieDr(): ?string
    {
        return $this->recit_vieDr;
    }

    public function setRecitVieDr(?string $recit_vieDr): void
    {
        $this->recit_vieDr = $recit_vieDr;
    }

    public function getLiensHypertexteFr(): ?string
    {
        return $this->liens_hypertexteFr;
    }

    public function setLiensHypertexteFr(?string $liens_hypertexteFr): void
    {
        $this->liens_hypertexteFr = $liens_hypertexteFr;
    }

    public function getLiensHypertexteAr(): ?string
    {
        return $this->liens_hypertexteAr;
    }

    public function setLiensHypertexteAr(?string $liens_hypertexteAr): void
    {
        $this->liens_hypertexteAr = $liens_hypertexteAr;
    }

    public function getLiensHypertexteDr(): ?string
    {
        return $this->liens_hypertexteDr;
    }

    public function setLiensHypertexteDr(?string $liens_hypertexteDr): void
    {
        $this->liens_hypertexteDr = $liens_hypertexteDr;
    }

}
