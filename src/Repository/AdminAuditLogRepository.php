<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AdminAuditLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @brief Repository for admin audit log entries.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
class AdminAuditLogRepository extends ServiceEntityRepository
{
    /**
     * @brief AdminAuditLogRepository constructor.
     *
     * @param ManagerRegistry $registry The manager registry.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminAuditLog::class);
    }

    /**
     * @brief Finds recent audit rows, newest first.
     *
     * @param int $limit Maximum rows.
     * @return AdminAuditLog[] The entries.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function findAllOrdered(int $limit = 300): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.createdBy', 'u')
            ->addSelect('u')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
