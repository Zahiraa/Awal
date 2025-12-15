<?php

namespace App\Entity;

use App\Repository\TermeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TermeRepository::class)]
class Terme
{

    const STATUT_DRAFT = 'draft';
    const STATUT_PUBLISHED = 'published';
    const STATUT_WAITING_APPROVAL = 'waiting_approval';

    use TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'approuvedTermes')]
    private ?User $approvedBy = null;

    #[ORM\ManyToOne(inversedBy: 'createdTerme')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\ManyToOne]
    private ?User $updatedBy = null;

    /**
     * @var Collection<int, Content>
     */
    #[ORM\OneToMany(targetEntity: Content::class, mappedBy: 'terme', cascade: ['persist', 'remove'])]
    private Collection $contents;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?File $image = null;

    #[ORM\OneToOne(mappedBy: 'terme', cascade: ['persist', 'remove'])]
    private ?Proposition $proposition = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $approvedAt = null;

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

    public function getApprovedBy(): ?User
    {
        return $this->approvedBy;
    }

    public function setApprovedBy(?User $approvedBy): static
    {
        $this->approvedBy = $approvedBy;

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

    public function getUpdatedBy(): ?User
    {
        try {
            return ($this->updatedBy && $this->updatedBy->getDeletedAt() === null) ? $this->updatedBy : null;

        } catch (\Doctrine\ORM\EntityNotFoundException $e) {
            return null;
        }
    }

    public function setUpdatedBy(?User $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

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
            $content->setTerme($this);
        }

        return $this;
    }

    public function removeContent(Content $content): static
    {
        if ($this->contents->removeElement($content)) {
            // set the owning side to null (unless already changed)
            if ($content->getTerme() === $this) {
                $content->setTerme(null);
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

    public function getProposition(): ?Proposition
    {
        return $this->proposition;
    }

    public function setProposition(?Proposition $proposition): static
    {
        // unset the owning side of the relation if necessary
        if ($proposition === null && $this->proposition !== null) {
            $this->proposition->setTerme(null);
        }

        // set the owning side of the relation if necessary
        if ($proposition !== null && $proposition->getTerme() !== $this) {
            $proposition->setTerme($this);
        }

        $this->proposition = $proposition;

        return $this;
    }

    public function getApprovedAt(): ?\DateTime
    {
        return $this->approvedAt;
    }

    public function setApprovedAt(?\DateTime $approvedAt): static
    {
        $this->approvedAt = $approvedAt;

        return $this;
    }
}
