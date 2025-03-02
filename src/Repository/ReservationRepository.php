<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Récupère toutes les réservations triées par date de réservation.
     *
     * @param string $order 'ASC' pour croissant, 'DESC' pour décroissant
     * @return Reservation[]
     */
    public function findByDateReservation(string $order = 'ASC'): array
    {
        // Sécurisation du tri pour éviter les injections SQL
        $order = strtoupper($order);
        if (!in_array($order, ['ASC', 'DESC'])) {
            $order = 'ASC'; // Valeur par défaut
        }

        return $this->createQueryBuilder('r')
            ->orderBy('r.Date_Reservation', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre total de réservations.
     *
     * @return int
     */
    public function countReservations(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte le nombre de réservations par mois.
     *
     * @return array
     */
    public function countReservationsByMonth(): array
    {
        return $this->createQueryBuilder('r')
            ->select("SUBSTRING(r.Date_Reservation, 1, 7) AS month, COUNT(r.id) AS count")
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre de réservations par événement.
     *
     * @return array
     */
    public function countReservationsByEvent(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.Nom_Evenement AS eventName, COUNT(r.id) AS count')
            ->groupBy('r.Nom_Evenement')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findPastReservations(): array
{
    return $this->createQueryBuilder('r')
        ->where('r.Date_Reservation < :today') // Assure-toi que le nom du champ dans ta base est correct
        ->setParameter('today', new \DateTime())
        ->orderBy('r.Date_Reservation', 'DESC')
        ->getQuery()
        ->getResult();
}

    

}
