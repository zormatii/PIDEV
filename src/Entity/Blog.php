<?php
namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BlogRepository::class)]
class Blog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Please enter your title")]
    #[Assert\Length(
        min: 5, 
        max: 255, 
        minMessage: "The title must be at least {{ limit }} characters long.",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Please enter your content.")]
    #[Assert\Length(
        min: 10, 
        max: 1000, 
        minMessage: "The content must be at least {{ limit }} characters long.",
        maxMessage: "The content cannot exceed {{ limit }} characters."
    )]
    private ?string $contenu = null;

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $image = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "The creation date is mandatory.")]
    #[Assert\Type(\DateTimeInterface::class, message: "The creation date must be provided")]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The name of creator is necessary.")]
    #[Assert\Length(
        min: 3, 
        max: 100, 
        minMessage: "The name of creator must be at least {{ limit }} characters.",
        maxMessage: "Le nom du créateur ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $createur_article = null;

    #[ORM\OneToMany(mappedBy: 'blog', targetEntity: Commentaire::class, cascade: ['remove'])]
    private Collection $commentaires;

    #[ORM\Column(type: 'boolean')]
    private ?bool $favori = false;

    #[ORM\Column(type: 'integer')]
    private ?int $likes = 0;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->date_creation = new \DateTime();  // Définir la date de création par défaut
    }

    // Getters et Setters

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

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;
        return $this;
    }

    public function getCreateurArticle(): ?string
    {
        return $this->createur_article;
    }

    public function setCreateurArticle(string $createur_article): static
    {
        $this->createur_article = $createur_article;
        return $this;
    }

    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setBlog($this);  // Associer le commentaire au blog
        }
        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // On retire le lien entre le commentaire et le blog
            if ($commentaire->getBlog() === $this) {
                $commentaire->setBlog(null);
            }
        }
        return $this;
    }

    public function isFavori(): ?bool
    {
        return $this->favori;
    }

    public function setFavori(bool $favori): self
    {
        $this->favori = $favori;

        return $this;
    }



    public function getLikes(): ?int
{
    return $this->likes;
}



public function setLikes(int $likes): self
{
    $this->likes = $likes;

    return $this;
}

public function addLike(): self
{
    $this->likes++;
    return $this;
}

public function removeLike(): self
{
    $this->likes--;
    return $this;
}
}

