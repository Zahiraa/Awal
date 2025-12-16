<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ContactDTO
{
    #[Assert\NotBlank(message: 'Nom ne peut pas être vide.')]
    private ?string $nom = null;

    #[Assert\NotBlank(message: 'Prénom ne peut pas être vide.')]
    private ?string $prenom = null;

    #[Assert\NotBlank(message: 'Email ne peut pas être vide.')]
    #[Assert\Email(message: 'L\'email {{ value }} n\'est pas un email valide.')]
    private ?string $email = null;

    #[Assert\NotBlank(message: 'Le sujet ne peut pas être vide.')]
    private ?string $subject = null;

    #[Assert\NotBlank(message: 'Message ne peut pas être vide.')]
    private ?string $message = null;

   


    // Getters
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }



    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    // Setters
    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }



    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }
 
} 