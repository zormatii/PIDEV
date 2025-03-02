<?php

namespace App\Repository;

use App\Entity\Collaboration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Collaboration>
 */
class CollaborationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collaboration::class);
    }

    //    /**
    //     * @return Collaboration[] Returns an array of Collaboration objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Collaboration
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function searchCollaborations(string $query): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.nom_c LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    public function findBySortedField(string $field, string $order): array
    {
        $allowedFields = ['nom_c', 'date_sig', 'date_ex', 'type', 'status']; // Champs autorisés
        if (!in_array($field, $allowedFields)) {
            $field = 'date_sig'; // Valeur par défaut
        }
    
        $order = strtoupper($order);
        if (!in_array($order, ['ASC', 'DESC'])) {
            $order = 'ASC'; // Valeur par défaut
        }
    
        return $this->createQueryBuilder('c')
            ->orderBy("c.$field", $order)
            ->getQuery()
            ->getResult();
    }
    



}
