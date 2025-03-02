<?php

namespace App\Entity;

use App\Repository\TypeEvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TypeEvenementRepository::class)]
class TypeEvenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est requis.")]
    private ?string $nom = null;
    
    // Chaque type d'évènement appartient à une seule catégorie
    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: "typeEvenements")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url_image = null;
    
    // Chaque type d'évènement peut contenir plusieurs évènements
    #[ORM\OneToMany(mappedBy: "typeEvenement", targetEntity: Evenement::class, cascade: ["remove"])]
    private Collection $evenements;

    #[ORM\Column(length: 255)]
    private ?string $description = null;
    
    public function __construct()
    {
        $this->evenements = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getNom(): ?string
    {
        return $this->nom;
    }
    
    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }
    
    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }
    
    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }
    
    /**
     * @return Collection<int, Evenement>
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }
    
    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements[] = $evenement;
            $evenement->setTypeEvenement($this);
        }
        return $this;
    }
    
    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            // Met à jour la relation côté propriétaire
            if ($evenement->getTypeEvenement() === $this) {
                $evenement->setTypeEvenement(null);
            }
        }
        return $this;
    }
    public function getUrlImage(): ?string {
        return $this->url_image;
    }

    public function setUrlImage(?string $url_image): static {
        $this->url_image = $url_image;
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
}
