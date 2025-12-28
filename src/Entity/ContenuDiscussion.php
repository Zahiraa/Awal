<?php

namespace App\Entity;

use App\Repository\ContenuDiscussionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContenuDiscussionRepository::class)]
class ContenuDiscussion
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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subTitle = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?File $image = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?File $media = null;

    #[ORM\ManyToOne(inversedBy: 'contenuDiscussions')]
    private ?Contenu $contenu = null;

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

    public function getSubTitle(): ?string
    {
        return $this->subTitle;
    }

    public function setSubTitle(?string $subTitle): static
    {
        $this->subTitle = $subTitle;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

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

    public function getMedia(): ?File
    {
        return $this->media;
    }

    public function setMedia(?File $media): static
    {
        $this->media = $media;

        return $this;
    }

    public function getContenu(): ?Contenu
    {
        return $this->contenu;
    }

    public function setContenu(?Contenu $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }
}
