<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The name is mandatory.")]
    #[Assert\Length(min: 3, max: 255, minMessage: "The name must be at least {{ limit }} characters long.")]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The first name is mandatory.")]
    #[Assert\Length(min: 3, max: 255, minMessage: "The first name must be at least {{ limit }} characters long.")]
    private ?string $Prenom = null;

    #[ORM\Column(length: 8)]  // Modification ici
    #[Assert\NotBlank(message: "The phone number is mandatory.")]
    #[Assert\Regex(
        pattern: "/^\d{8}$/",
        message: "The phone number must be exactly 8 digits long."
    )]
    private ?string $Numero_Telephone = null;  // Changement de int -> string

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The email is mandatory.")]
    #[Assert\Email(message: "The email address '{{ value }}' is not valid.")]
    #[Assert\Length(max: 255, maxMessage: "The email address cannot be longer than {{ limit }} characters.")]
    private ?string $Email = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "The date is mandatory.")]
    #[Assert\Type(\DateTimeInterface::class, message: "Please enter a valid date.")]
    private ?\DateTimeInterface $date = null;


    public function __construct()
    {
        // Set the default to the current date/time
        $this->date = new \DateTime(); // Default to now
    }
    #[ORM\Column]
    #[Assert\NotBlank(message: "The price is mandatory.")]
    #[Assert\Positive(message: "The price must be a positive number.")]
    private ?int $Prix = null;

    #[ORM\ManyToOne(targetEntity: Reservation::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reservation $reservation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): static
    {
        $this->Prenom = $Prenom;
        return $this;
    }

    public function getNumeroTelephone(): ?string  // Modification ici
    {
        return $this->Numero_Telephone;
    }

    public function setNumeroTelephone(string $Numero_Telephone): static  // Modification ici
    {
        $this->Numero_Telephone = $Numero_Telephone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): static
    {
        $this->Email = $Email;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }
    
    public function getPrix(): ?int
    {
        return $this->Prix;
    }

    public function setPrix(int $Prix): static
    {
        $this->Prix = $Prix;
        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;
        return $this;
    }
}
