<?php

namespace App\Repository;

use App\Entity\ServicesWhyCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @brief ServicesWhyCard repository.
 *
 * @date 2026-03-21
 * @author Stephane H.
 */
class ServicesWhyCardRepository extends ServiceEntityRepository
{
    /**
     * @brief ServicesWhyCardRepository constructor.
     *
     * @param ManagerRegistry $registry The manager registry.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServicesWhyCard::class);
    }

    /**
     * @brief Finds all cards ordered by position.
     *
     * @return ServicesWhyCard[] The ordered cards.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.position', 'ASC')
            ->addOrderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
