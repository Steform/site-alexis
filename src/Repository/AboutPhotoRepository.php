<?php

namespace App\Repository;

use App\Entity\AboutPhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @brief AboutPhoto repository.
 *
 * @date 2026-03-18
 * @author Stephane H.
 */
class AboutPhotoRepository extends ServiceEntityRepository
{
    /**
     * @brief AboutPhotoRepository constructor.
     *
     * @param ManagerRegistry $registry The manager registry.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AboutPhoto::class);
    }

    /**
     * @brief Finds active photos ordered by position.
     *
     * @return AboutPhoto[] The ordered active photos.
     * @date 2026-03-18
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
     * @brief Finds all photos ordered by position (for back-office management).
     *
     * @return AboutPhoto[] The ordered photos.
     * @date 2026-03-18
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
     * @brief Returns the next position value for a new photo (max + 1, or 0 if none).
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

