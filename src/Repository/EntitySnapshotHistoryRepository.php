<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EntitySnapshotHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @brief Repository for EntitySnapshotHistory.
 *
 * @extends ServiceEntityRepository<EntitySnapshotHistory>
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
class EntitySnapshotHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntitySnapshotHistory::class);
    }

    /**
     * @brief Returns newest rows for merged timeline.
     *
     * @param int $limit Max rows.
     * @return EntitySnapshotHistory[]
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function findAllOrdered(int $limit): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
