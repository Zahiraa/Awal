<?php

namespace App\Entity;

use App\Repository\PropositionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PropositionRepository::class)]
class Proposition
{
    use TimestampTrait;

    const STATUT_APPROVED = 'approved';
    const STATUT_WAITING_APPROVAL = 'waiting_approval';
    const STATUT_REJECTED = 'rejected';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\ManyToOne]
    private ?User $actionBy = null;

    #[ORM\ManyToOne(inversedBy: 'propositions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    /**
     * @var Collection<int, Content>
     */
    #[ORM\OneToMany(targetEntity: Content::class, mappedBy: 'proposition', cascade: ['persist', 'remove'])]
    private Collection $contents;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?File $image = null;

    #[ORM\OneToOne(inversedBy: 'proposition', cascade: ['persist', 'remove'])]
    private ?Terme $terme = null;

    public function __construct()
    {
        $this->contents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getActionBy(): ?User
    {
        return $this->actionBy;
    }

    public function setActionBy(?User $actionBy): static
    {
        $this->actionBy = $actionBy;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        try {
            return ($this->createdBy->getDeletedAt() === null) ? $this->createdBy : null;

        } catch (\Doctrine\ORM\EntityNotFoundException $e) {
            return null;
        }
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection<int, Content>
     */
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(Content $content): static
    {
        if (!$this->contents->contains($content)) {
            $this->contents->add($content);
            $content->setProposition($this);
        }

        return $this;
    }

    public function removeContent(Content $content): static
    {
        if ($this->contents->removeElement($content)) {
            // set the owning side to null (unless already changed)
            if ($content->getProposition() === $this) {
                $content->setProposition(null);
            }
        }

        return $this;
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setImage(?File $image): static
    {
        $this->image = $image;

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
}
