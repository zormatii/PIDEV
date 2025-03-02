<?php

namespace App\Entity;

use App\Repository\CollaborationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CollaborationRepository::class)]
class Collaboration
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // Automatically generate value for id
    #[ORM\Column]
    private ?int $id = null;

   
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(min: 5, max: 255, minMessage: "Le nom doit contenir au moins 5 caractères.")]
    private ?string $nom_c = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le type est obligatoire.")]
    #[Assert\Length(min: 5, max: 255, minMessage: "Le type doit contenir au moins 5 caractères.")]
    private ?string $type = null;


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de signature est obligatoire.")]
    #[Assert\GreaterThanOrEqual('today', message: "La date de signature ne peut pas être dans le passé.")]
    private ?\DateTimeInterface $date_sig = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date d'expiration est obligatoire.")]
    #[Assert\GreaterThanOrEqual('today', message: "La date d'expiration ne peut pas être dans le passé.")]
    private ?\DateTimeInterface $date_ex = null;

    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le statut est obligatoire.")]
    #[Assert\Choice(choices: ['active', 'expired'], message: "Le statut doit être soit 'Active' ou 'Expired'.")]
    private ?string $status = null;


    private ?string $pdfFile = null;



    /**
     * @var Collection<int, Partenaire>
     */
    #[ORM\OneToMany(targetEntity: Partenaire::class, mappedBy: 'collaboration', cascade: ['remove'])]
    private Collection $partenaire;

    public function __construct()
    {
        $this->partenaire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    

    public function getNomC(): ?string
    {
        return $this->nom_c;
    }

    public function setNomC(string $nom_c): static
    {
        $this->nom_c = $nom_c;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getDateSig(): ?\DateTimeInterface
    {
        return $this->date_sig;
    }

    public function setDateSig(\DateTimeInterface $date_sig): static
{
    $this->date_sig = $date_sig;
    return $this;
}


    public function getDateEx(): ?\DateTimeInterface
    {
        return $this->date_ex;
    }

    public function setDateEx(\DateTimeInterface $date_ex): static
    {
        $this->date_ex = $date_ex;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }
    public function getPdfFile(): ?string
{
    return $this->pdfFile;
}

public function setPdfFile(?string $pdfFile): static
{
    $this->pdfFile = $pdfFile;
    return $this;
}

    /**
     * @return Collection<int, Partenaire>
     */
    public function getPartenaire(): Collection
    {
        return $this->partenaire;
    }

    public function addPartenaire(Partenaire $partenaire): static
    {
        if (!$this->partenaire->contains($partenaire)) {
            $this->partenaire->add($partenaire);
            $partenaire->setCollaboration($this);
        }
        return $this;
    }

    public function removePartenaire(Partenaire $partenaire): static
    {
        if ($this->partenaire->removeElement($partenaire)) {
            // set the owning side to null (unless already changed)
            if ($partenaire->getCollaboration() === $this) {
                $partenaire->setCollaboration(null);
            }
        }
        return $this;
    }
}
