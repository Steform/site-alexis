<?php

namespace App\Repository;

use App\Entity\Horaires;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Horaires repository.
 *
 * @author Stephane H.
 * @created 2026-03-11
 *
 * @inputs  ManagerRegistry
 * @outputs Horaires entity queries
 */
class HorairesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Horaires::class);
    }

    /**
     * Get all horaires ordered by day order (lundi to dimanche).
     *
     * @return Horaires[]
     */
    public function findAllOrdered(): array
    {
        $results = $this->createQueryBuilder('h')
            ->orderBy('h.jour')
            ->getQuery()
            ->getResult();

        $order = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
        usort($results, fn (Horaires $a, Horaires $b) => array_search($a->getJour(), $order) <=> array_search($b->getJour(), $order));

        return $results;
    }
}
