<?php

namespace App\Entity;

use App\Repository\PartenaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartenaireRepository::class)]
class Partenaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true, type: 'string')]  // ID P
    private ?string $id_p = null;

    #[ORM\Column(length: 255)]  // Name
    private ?string $nom_p = null;

    #[ORM\Column(length: 255)]  // Email
    private ?string $email_p = null;

    #[ORM\Column(length: 255)]  // Telephone
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]  // Type
    private ?string $type_p = null;

    #[ORM\ManyToOne(inversedBy: 'partenaire')]
    private ?Collaboration $collaboration = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;  // Verification status

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $verificationToken = null;  // Token for verification

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdP(): ?string
    {
        return $this->id_p;
    }

    public function setIdP(?string $id_p): static
    {
        $this->id_p = $id_p;
        return $this;
    }

    public function getNomP(): ?string
    {
        return $this->nom_p;
    }

    public function setNomP(string $nom_p): static
    {
        $this->nom_p = $nom_p;
        return $this;
    }

    public function getEmailP(): ?string
    {
        return $this->email_p;
    }

    public function setEmailP(string $email_p): static
    {
        $this->email_p = $email_p;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getTypeP(): ?string
    {
        return $this->type_p;
    }

    public function setTypeP(string $type_p): static
    {
        $this->type_p = $type_p;
        return $this;
    }

    public function getCollaboration(): ?Collaboration
    {
        return $this->collaboration;
    }

    public function setCollaboration(?Collaboration $collaboration): static
    {
        $this->collaboration = $collaboration;
        return $this;
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

    public function getVerificationToken(): ?string
    {
        return $this->verificationToken;
    }

    public function setVerificationToken(?string $verificationToken): static
    {
        $this->verificationToken = $verificationToken;
        return $this;
    }
}