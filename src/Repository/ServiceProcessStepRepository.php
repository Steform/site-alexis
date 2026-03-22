<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Service;
use App\Entity\ServiceProcessStep;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @brief ServiceProcessStep repository.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
class ServiceProcessStepRepository extends ServiceEntityRepository
{
    /**
     * @brief ServiceProcessStepRepository constructor.
     *
     * @param ManagerRegistry $registry The manager registry.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceProcessStep::class);
    }

    /**
     * @brief Counts steps for a service.
     *
     * @param Service $service The service.
     * @return int The number of steps.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function countByService(Service $service): int
    {
        return (int) $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.service = :service')
            ->setParameter('service', $service)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @brief Finds all steps for a service ordered by position.
     *
     * @param Service $service The service.
     * @return ServiceProcessStep[] The ordered steps.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function findByServiceOrdered(Service $service): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.service = :service')
            ->setParameter('service', $service)
            ->orderBy('s.position', 'ASC')
            ->addOrderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
