<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;

use DateTime;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email', groups: ['registration'])]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    use TimestampTrait;
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

    const DELETED = 'deleted';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $registrationToken = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $invitedAt = null;

    /**
     * @var Collection<int, Terme>
     */
    #[ORM\OneToMany(targetEntity: Terme::class, mappedBy: 'approuvedBy')]
    private Collection $approvedTermes;

    /**
     * @var Collection<int, Terme>
     */
    #[ORM\OneToMany(targetEntity: Terme::class, mappedBy: 'createdBy')]
    private Collection $createdTerme;

    /**
     * @var Collection<int, Proposition>
     */
    #[ORM\OneToMany(targetEntity: Proposition::class, mappedBy: 'createdBy')]
    private Collection $propositions;

    public function __construct()
    {
        $this->approvedTermes = new ArrayCollection();
        $this->createdTerme = new ArrayCollection();
        $this->propositions = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nom . ' ' . $this->prenom;
    }

    public function isManager(): bool
    {
        return in_array('ROLE_MANAGER', $this->roles) and !in_array('ROLE_ADMIN', $this->roles);
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->roles);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

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


    public function getRegistrationToken(): ?string
    {
        return $this->registrationToken;
    }

    public function setRegistrationToken(?string $registrationToken): self
    {
        $this->registrationToken = $registrationToken;
        return $this;
    }

    public function getInvitedAt(): ?\DateTimeInterface
    {
        return $this->invitedAt;
    }

    public function setInvitedAt(?\DateTimeInterface $invitedAt): self
    {
        $this->invitedAt = $invitedAt;
        return $this;
    }

    /**
     * @return Collection<int, Terme>
     */
    public function getApprovedTermes(): Collection
    {
        return $this->approvedTermes;
    }

    public function addApprouvedTerme(Terme $approuvedTerme): static
    {
        if (!$this->approvedTermes->contains($approuvedTerme)) {
            $this->approvedTermes->add($approuvedTerme);
            $approuvedTerme->setApprovedBy($this);
        }

        return $this;
    }

    public function removeApprouvedTerme(Terme $approuvedTerme): static
    {
        if ($this->approvedTermes->removeElement($approuvedTerme)) {
            // set the owning side to null (unless already changed)
            if ($approuvedTerme->getApprovedBy() === $this) {
                $approuvedTerme->setApprovedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Terme>
     */
    public function getCreatedTerme(): Collection
    {
        return $this->createdTerme;
    }

    public function addCreatedTerme(Terme $createdTerme): static
    {
        if (!$this->createdTerme->contains($createdTerme)) {
            $this->createdTerme->add($createdTerme);
            $createdTerme->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreatedTerme(Terme $createdTerme): static
    {
        if ($this->createdTerme->removeElement($createdTerme)) {
            // set the owning side to null (unless already changed)
            if ($createdTerme->getCreatedBy() === $this) {
                $createdTerme->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Proposition>
     */
    public function getPropositions(): Collection
    {
        return $this->propositions;
    }

    public function addProposition(Proposition $proposition): static
    {
        if (!$this->propositions->contains($proposition)) {
            $this->propositions->add($proposition);
            $proposition->setCreatedBy($this);
        }

        return $this;
    }

    public function removeProposition(Proposition $proposition): static
    {
        if ($this->propositions->removeElement($proposition)) {
            // set the owning side to null (unless already changed)
            if ($proposition->getCreatedBy() === $this) {
                $proposition->setCreatedBy(null);
            }
        }

        return $this;
    }
}
