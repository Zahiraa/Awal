<?php

namespace App\Entity;

use App\Repository\ContenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContenuRepository::class)]
class Contenu
{
    use TimestampTrait;

        const STATUT_DRAFT = 'draft';

    const STATUT_WAITING_APPROVAL = 'waiting_approval';

    const STATUT_PUBLISHED = 'published';

    const STATUTS = [
        self::STATUT_DRAFT,
        self::STATUT_WAITING_APPROVAL,
        self::STATUT_PUBLISHED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;


     #[ORM\Column(length: 255)]
    private ?string $statut = null;
    /**
     * @var Collection<int, ContenuNumero>
     */
    #[ORM\OneToMany(targetEntity: ContenuNumero::class, mappedBy: 'contenu')]
    private Collection $contenuNumeros;

    /**
     * @var Collection<int, ContenuDiscussion>
     */
    #[ORM\OneToMany(targetEntity: ContenuDiscussion::class, mappedBy: 'contenu')]
    private Collection $contenuDiscussions;

    public function __construct()
    {
        $this->contenuNumeros = new ArrayCollection();
        $this->contenuDiscussions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

        public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, ContenuNumero>
     */
    public function getContenuNumeros(): Collection
    {
        return $this->contenuNumeros;
    }

    public function addContenuNumero(ContenuNumero $contenuNumero): static
    {
        if (!$this->contenuNumeros->contains($contenuNumero)) {
            $this->contenuNumeros->add($contenuNumero);
            $contenuNumero->setContenu($this);
        }

        return $this;
    }

    public function removeContenuNumero(ContenuNumero $contenuNumero): static
    {
        if ($this->contenuNumeros->removeElement($contenuNumero)) {
            // set the owning side to null (unless already changed)
            if ($contenuNumero->getContenu() === $this) {
                $contenuNumero->setContenu(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ContenuDiscussion>
     */
    public function getContenuDiscussions(): Collection
    {
        return $this->contenuDiscussions;
    }

    public function addContenuDiscussion(ContenuDiscussion $contenuDiscussion): static
    {
        if (!$this->contenuDiscussions->contains($contenuDiscussion)) {
            $this->contenuDiscussions->add($contenuDiscussion);
            $contenuDiscussion->setContenu($this);
        }

        return $this;
    }

    public function removeContenuDiscussion(ContenuDiscussion $contenuDiscussion): static
    {
        if ($this->contenuDiscussions->removeElement($contenuDiscussion)) {
            // set the owning side to null (unless already changed)
            if ($contenuDiscussion->getContenu() === $this) {
                $contenuDiscussion->setContenu(null);
            }
        }

        return $this;
    }
}
