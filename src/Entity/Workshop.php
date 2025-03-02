<?php

namespace App\Entity;

use App\Repository\WorkshopRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WorkshopRepository::class)]
class Workshop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    #[Assert\Length(min: 5, max: 255, minMessage: "Le titre doit contenir au moins {{ limit }} caractères.")]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(min: 10, max: 500, minMessage: "La description doit contenir au moins {{ limit }} caractères.")]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date est obligatoire.")]
    #[Assert\Type(\DateTimeInterface::class, message: "Veuillez entrer une date valide.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le lieu est obligatoire.")]
    private ?string $lieu = null;

    #[ORM\Column]
    #[Assert\Positive(message: "La capacité doit être un nombre positif.")]
    #[Assert\GreaterThan(value: 0, message: "La capacité doit être supérieure à 0.")]
    private ?int $capicite = null;

    #[ORM\Column]    
    #[Assert\NotBlank(message: "La durée est obligatoire.")]
    #[Assert\Positive(message: "La durée doit être un nombre positif.")]
    #[Assert\GreaterThan(value: 0, message: "La durée doit être supérieure à 0.")]
    private ?int $duree = null;

    #[ORM\OneToOne(mappedBy: 'workshop', cascade: ['persist', 'remove'])]
    private ?Formation $formation = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getCapicite(): ?int
    {
        return $this->capicite;
    }

    public function setCapicite(int $capicite): static
    {
        $this->capicite = $capicite;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        // unset the owning side of the relation if necessary
        if ($formation === null && $this->formation !== null) {
            $this->formation->setWorkshop(null);
        }

        // set the owning side of the relation if necessary
        if ($formation !== null && $formation->getWorkshop() !== $this) {
            $formation->setWorkshop($this);
        }

        $this->formation = $formation;

        return $this;
    }
}
