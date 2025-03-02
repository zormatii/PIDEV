<?php
namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\TypeEvenement;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The type field is required.")]
    #[Assert\Regex(pattern: "/^[^\d]+$/", message: "The type can only contain letters and must not include numbers.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The description field is required.")]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean')]
    private bool $statut = true;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url_image = null;

    /**
     * @var Collection<int, TypeEvenement>
     */
    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: TypeEvenement::class, cascade: ['remove'])]
    private Collection $typeEvenements;


    public function __construct() {
        $this->typeEvenements = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(string $nom): static {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(string $description): static {
        $this->description = $description;
        return $this;
    }

    public function isStatut(): bool {
        return $this->statut;
    }

    public function setStatut(bool $statut): static {
        $this->statut = $statut;
        return $this;
    }

    public function getUrlImage(): ?string {
        return $this->url_image;
    }

    public function setUrlImage(?string $url_image): static {
        $this->url_image = $url_image;
        return $this;
    }

    /**
     * @return Collection<int, TypeEvenement>
     */
    public function getTypeEvenements(): Collection
    {
        return $this->typeEvenements;
    }

    public function addTypeEvenement(TypeEvenement $typeEvenement): static
    {
        if (!$this->typeEvenements->contains($typeEvenement)) {
            $this->typeEvenements->add($typeEvenement);
            $typeEvenement->setCategorie($this);
        }

        return $this;
    }

    public function removeTypeEvenement(TypeEvenement $typeEvenement): static
    {
        if ($this->typeEvenements->removeElement($typeEvenement)) {
            // set the owning side to null (unless already changed)
            if ($typeEvenement->getCategorie() === $this) {
                $typeEvenement->setCategorie(null);
            }
        }

        return $this;
    }
}