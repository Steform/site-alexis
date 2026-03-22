<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UploadDeletionHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @brief Repository for upload deletion history entries.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
class UploadDeletionHistoryRepository extends ServiceEntityRepository
{
    /**
     * @brief UploadDeletionHistoryRepository constructor.
     *
     * @param ManagerRegistry $registry The manager registry.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UploadDeletionHistory::class);
    }

    /**
     * @brief Finds recent upload deletion records, newest first.
     *
     * @param int $limit Maximum rows.
     * @return UploadDeletionHistory[] The entries.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function findAllOrdered(int $limit = 200): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.createdBy', 'usr')
            ->addSelect('usr')
            ->orderBy('u.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
