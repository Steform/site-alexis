<?php

namespace App\Repository;

use App\Entity\AboutSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @brief AboutSection repository.
 *
 * @date 2026-03-18
 * @author Stephane H.
 */
class AboutSectionRepository extends ServiceEntityRepository
{
    /**
     * @brief AboutSectionRepository constructor.
     *
     * @param ManagerRegistry $registry The manager registry.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AboutSection::class);
    }

    /**
     * @brief Finds the singleton about section.
     *
     * @return AboutSection|null The singleton AboutSection or null if not found.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function findSingleton(): ?AboutSection
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

