<?php

namespace App\Repository;

use App\Entity\ContentBlockHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @brief Repository for content block history entries.
 *
 * @date 2026-03-19
 * @author Stephane H.
 */
class ContentBlockHistoryRepository extends ServiceEntityRepository
{
    /**
     * @brief ContentBlockHistoryRepository constructor.
     *
     * @param ManagerRegistry $registry The manager registry.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContentBlockHistory::class);
    }

    /**
     * @brief Finds history entries for a page and locale, ordered by date descending.
     *
     * @param string $pageName The page name.
     * @param string $locale The locale.
     * @param int $limit Maximum number of entries.
     * @return ContentBlockHistory[] The matching entries.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function findByPageAndLocale(string $pageName, string $locale, int $limit = 100): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.pageName = :pageName')
            ->andWhere('h.locale = :locale')
            ->setParameter('pageName', $pageName)
            ->setParameter('locale', $locale)
            ->orderBy('h.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @brief Finds all history entries ordered by date descending.
     *
     * @param int $limit Maximum number of entries.
     * @param string|null $pageName Optional page filter.
     * @param string|null $locale Optional locale filter.
     * @return ContentBlockHistory[] The matching entries.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function findAllOrdered(int $limit = 200, ?string $pageName = null, ?string $locale = null): array
    {
        $qb = $this->createQueryBuilder('h')
            ->leftJoin('h.createdBy', 'u')
            ->addSelect('u')
            ->orderBy('h.createdAt', 'DESC')
            ->setMaxResults($limit);

        if ($pageName !== null && $pageName !== '') {
            $qb->andWhere('h.pageName = :pageName')->setParameter('pageName', $pageName);
        }
        if ($locale !== null && $locale !== '') {
            $qb->andWhere('h.locale = :locale')->setParameter('locale', $locale);
        }

        return $qb->getQuery()->getResult();
    }
}
