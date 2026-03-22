<?php

namespace App\Repository;

use App\Entity\Coordinates;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @brief Repository for Coordinates entity.
 *
 * @date 2026-03-16
 * @author Stephane H.
 *
 * @extends ServiceEntityRepository<Coordinates>
 */
class CoordinatesRepository extends ServiceEntityRepository
{
    /**
     * @brief CoordinatesRepository constructor.
     *
     * @param ManagerRegistry $registry The manager registry instance.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coordinates::class);
    }

    /**
     * @brief Returns the single coordinates record, if any.
     *
     * @return Coordinates|null The coordinates or null.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function findSingle(): ?Coordinates
    {
        return $this->createQueryBuilder('c')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

