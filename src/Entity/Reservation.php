<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The event name is mandatory.")]
    #[Assert\Length(min: 5, max: 255, minMessage: "The name must contain at least {{ limit }} characters.")]
    private ?string $Nom_Evenement = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "Veuillez entrer une adresse email valide.")]
    private ?string $email = null;
    
    #[ORM\Column]
    #[Assert\NotBlank(message: "Please enter the number of tickets.")]
    private ?int $Nombre_Tickets = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "The date is mandatory.")]
    #[Assert\Type(\DateTimeInterface::class, message: "Please enter a valid date.")]
    #[Assert\GreaterThanOrEqual("today", message: "The reservation date must not be in the past.")]
    private ?\DateTimeInterface $Date_Reservation = null;
   

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank(message: "The time is mandatory.")]
    #[Assert\Type(\DateTimeInterface::class, message: "Please enter a valid time.")]
    private ?\DateTimeInterface $Heure = null;
    

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'reservation')]
    private Collection $id_ticket;

    public function __construct()
    {
        $this->id_ticket = new ArrayCollection();
    }


    public function getNomEvenement(): ?string
    {
        return $this->Nom_Evenement;
    }

    public function setNomEvenement(string $Nom_Evenement): static
    {
        $this->Nom_Evenement = $Nom_Evenement;

        return $this;
    }
    public function getEmail(): ?string
    {
       return $this->email;
    }

    public function setEmail(string $email): static
    {
       $this->email = $email;
       return $this;
    }

    public function getNombreTickets(): ?int
    {
        return $this->Nombre_Tickets;
    }

    public function setNombreTickets(int $Nombre_Tickets): static
    {
        $this->Nombre_Tickets = $Nombre_Tickets;

        return $this;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->Date_Reservation;
    }
    public function setDateReservation(?\DateTimeInterface $Date_Reservation): self
    {
        $this->Date_Reservation = $Date_Reservation;
    
        return $this;
    }
    
    public function getHeure(): ?\DateTimeInterface
    {
        return $this->Heure;
    }

    public function setHeure(?\DateTimeInterface $Heure): static
{
    $this->Heure = $Heure;
    return $this;
}

    /**
     * @return Collection<int, Ticket>
     */
    public function getIdTicket(): Collection
    {
        return $this->id_ticket;
    }

    public function addIdTicket(Ticket $idTicket): static
    {
        if (!$this->id_ticket->contains($idTicket)) {
            $this->id_ticket->add($idTicket);
            $idTicket->setReservation($this);
        }

        return $this;
    }

    public function removeIdTicket(Ticket $idTicket): static
    {
        if ($this->id_ticket->removeElement($idTicket)) {
            // set the owning side to null (unless already changed)
            if ($idTicket->getReservation() === $this) {
                $idTicket->setReservation(null);
            }
        }

        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    
}