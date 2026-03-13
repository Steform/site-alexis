<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Message repository.
 *
 * @author Stephane H.
 * @created 2026-03-11
 *
 * @inputs  ManagerRegistry
 * @outputs Message entity queries
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Get all messages currently active (within date range).
     *
     * @return Message[]
     */
    public function findActive(\DateTimeInterface $now = null): array
    {
        $now = $now ?? new \DateTimeImmutable();

        return $this->createQueryBuilder('m')
            ->andWhere('m.dateDebut <= :now')
            ->andWhere('m.dateFin >= :now')
            ->setParameter('now', $now)
            ->orderBy('m.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
