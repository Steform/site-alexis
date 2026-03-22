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

    /**
     * @brief Returns the next position value for a new hero photo (max + 1, or 0 if none).
     *
     * @return int The next position index.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getNextPosition(): int
    {
        $max = $this->createQueryBuilder('p')
            ->select('MAX(p.position)')
            ->getQuery()
            ->getSingleScalarResult();

        return $max === null ? 0 : (int) $max + 1;
    }
}

