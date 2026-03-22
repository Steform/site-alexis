<?php

namespace App\Repository;

use App\Entity\DevisTypePrestation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * DevisTypePrestation repository.
 *
 * @author Stephane H.
 * @date 2026-03-19
 */
class DevisTypePrestationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevisTypePrestation::class);
    }

    /**
     * Get active types ordered by ordre then id.
     *
     * @return DevisTypePrestation[]
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
     * @return DevisTypePrestation[]
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
