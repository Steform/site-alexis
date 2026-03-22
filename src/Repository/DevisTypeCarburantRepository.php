<?php

namespace App\Repository;

use App\Entity\DevisTypeCarburant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * DevisTypeCarburant repository.
 *
 * @author Stephane H.
 * @date 2026-03-19
 */
class DevisTypeCarburantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevisTypeCarburant::class);
    }

    /**
     * Get active types ordered by ordre then id.
     *
     * @return DevisTypeCarburant[]
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.actif = :actif')
            ->setParameter('actif', true)
            ->orderBy('t.ordre', 'ASC')
            ->addOrderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all types ordered by ordre then id.
     *
     * @return DevisTypeCarburant[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.ordre', 'ASC')
            ->addOrderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
