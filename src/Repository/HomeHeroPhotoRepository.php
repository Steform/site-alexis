<?php

namespace App\Repository;

use App\Entity\HomeHeroPhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @brief HomeHeroPhoto repository.
 *
 * @date 2026-03-19
 * @author Stephane H.
 */
class HomeHeroPhotoRepository extends ServiceEntityRepository
{
    /**
     * @brief HomeHeroPhotoRepository constructor.
     *
     * @param ManagerRegistry $registry The manager registry.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HomeHeroPhoto::class);
    }

    /**
     * @brief Finds active hero photos ordered by position.
     *
     * @return HomeHeroPhoto[] The ordered active photos.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.position', 'ASC')
            ->addOrderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @brief Finds all hero photos ordered by position.
     *
     * @return HomeHeroPhoto[] The ordered photos.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.position', 'ASC')
            ->addOrderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

