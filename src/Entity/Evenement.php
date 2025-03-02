<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\TypeEvenement;



#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The title field is required.")]
    #[Assert\Regex(pattern: "/^\D+$/", message: "The title cannot contain only numbers.")]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "The description field is required.")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The location field is required.")]
    private ?string $lieu = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "The number of seats cannot be empty.")]
    #[Assert\Positive(message: "The number of seats must be a positive value.")]
    private ?int $nombre_de_places = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "The start date is mandatory")]
    #[Assert\GreaterThanOrEqual("today", message: "The start date must not be in the past.")]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "The end date is mandatory.")]
    #[Assert\GreaterThan(propertyPath: "date_debut", message: "The end date must be after the start date.")]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(length: 20, options: ["default" => "en attente"])]
    #[Assert\Choice(choices: ["en attente", "accepté", "refusé"], message: "Invalid status.")]
    private ?string $statut = "en attente";

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url_image = null;

    #[ORM\ManyToOne(targetEntity: TypeEvenement::class, inversedBy: 'evenements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeEvenement $typeEvenement = null;

    #[ORM\Column(type: 'float')]
    private $latitude;

    #[ORM\Column(type: 'float')]
    private $longitude;

    #[ORM\Column(length: 255)]
    private ?string $mail = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitre(): ?string {
        return $this->titre;
    }

    public function setTitre(string $titre): static {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(string $description): static {
        $this->description = $description;
        return $this;
    }

    public function getLieu(): ?string {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static {
        $this->lieu = $lieu;
        return $this;
    }

    public function getNombreDePlaces(): ?int {
        return $this->nombre_de_places;
    }

    public function setNombreDePlaces(int $nombre_de_places): static {
        $this->nombre_de_places = $nombre_de_places;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface {
        return $this->date_debut;
    }

    public function setDateDebut(?\DateTimeInterface $date_debut): static {
        $this->date_debut = $date_debut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTimeInterface $date_fin): static {
        $this->date_fin = $date_fin;
        return $this;
    }

    public function getStatut(): ?string {
        return $this->statut;
    }

    public function setStatut(string $statut): static {
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

    public function getTypeEvenement(): ?TypeEvenement
    {
        return $this->typeEvenement;
    }

    public function setTypeEvenement(?TypeEvenement $typeEvenement): static
    {
        $this->typeEvenement = $typeEvenement;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }
}