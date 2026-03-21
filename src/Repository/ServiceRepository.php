<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Service repository.
 *
 * @author Stephane H.
 * @date 2026-03-19
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    /**
     * Get all services ordered by ordre then id.
     *
     * @return Service[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.ordre', 'ASC')
            ->addOrderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find a service by slug (FR) or slugDe (DE).
     *
     * @param string $slug The service slug (FR or DE).
     * @return Service|null The service or null if not found.
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function findBySlug(string $slug): ?Service
    {
        $service = $this->findOneBy(['slug' => $slug]);
        if ($service) {
            return $service;
        }

        return $this->findOneBy(['slugDe' => $slug]);
    }
}
