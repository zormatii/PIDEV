<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "The Comment Number is necessary.")]
    private ?int $idCommentaire = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Client Identifier is mandatory.")]
    private ?int $idclient = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Content of the comment should not be empty.")]
    #[Assert\Length(min: 5, minMessage: "The content must be at least {{ limit }} characters.")]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_depublication;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Author is necessary")]
    private ?string $auteur = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires', cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Blog $blog = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;


    // Ajout de la relation parent-enfant
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'reponses')]
    #[ORM\JoinColumn(onDelete: 'CASCADE', nullable: true)] 
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['remove'])]
    private Collection $reponses;

    // Ajout des propriétés likes et dislikes
    #[ORM\Column(type: 'integer')]
    private ?int $likes = 0;

    #[ORM\Column(type: 'integer')]
    private ?int $dislikes = 0;

    

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private $favori = false;

    public function __construct()
    {
        $this->date_depublication = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->reponses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCommentaire(): ?int
    {
        return $this->idCommentaire;
    }

    public function setIdCommentaire(int $idCommentaire): static
    {
        $this->idCommentaire = $idCommentaire;
        return $this;
    }

    public function getIdclient(): ?int
    {
        return $this->idclient;
    }

    public function setIdclient(int $idclient): static
    {
        $this->idclient = $idclient;
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

    public function getDateDepublication(): ?\DateTimeInterface
    {
        return $this->date_depublication;
    }

    public function setDateDepublication(\DateTimeInterface $date_depublication): static
    {
        $this->date_depublication = $date_depublication;
        return $this;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): static
    {
        $this->auteur = $auteur;
        return $this;
    }

    public function getBlog(): ?Blog
    {
        return $this->blog;
    }

    public function setBlog(?Blog $blog): static
    {
        $this->blog = $blog;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;
        return $this;
    }

    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(self $reponse): static
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses[] = $reponse;
            $reponse->setParent($this);
        }
        return $this;
    }

    public function removeReponse(self $reponse): static
    {
        if ($this->reponses->removeElement($reponse)) {
            if ($reponse->getParent() === $this) {
                $reponse->setParent(null);
            }
        }
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

    public function getDislikes(): ?int
    {
        return $this->dislikes;
    }

    public function setDislikes(int $dislikes): self
    {
        $this->dislikes = $dislikes;
        return $this;
    }

    public function isFavori(): bool
{
    return $this->favori;
}

public function setFavori(bool $favori): self
{
    $this->favori = $favori;
    return $this;
}

}
