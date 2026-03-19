<?php

namespace App\Repository;

use App\Entity\ContentBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @brief Repository for editable content blocks.
 *
 * @date 2026-03-18
 * @author Stephane H.
 */
class ContentBlockRepository extends ServiceEntityRepository
{
    /**
     * @brief ContentBlockRepository constructor.
     *
     * @param ManagerRegistry $registry The manager registry.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContentBlock::class);
    }

    /**
     * @brief Finds all blocks for a page and locale.
     *
     * @param string $pageName The page name.
     * @param string $locale The locale.
     * @return ContentBlock[] The matching blocks.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function findByPageAndLocale(string $pageName, string $locale): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.pageName = :pageName')
            ->andWhere('c.locale = :locale')
            ->setParameter('pageName', $pageName)
            ->setParameter('locale', $locale)
            ->orderBy('c.key', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @brief Finds one block by page, key and locale.
     *
     * @param string $pageName The page name.
     * @param string $key The block key.
     * @param string $locale The locale.
     * @return ContentBlock|null The matching block.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function findOneByComposite(string $pageName, string $key, string $locale): ?ContentBlock
    {
        return $this->findOneBy([
            'pageName' => $pageName,
            'key' => $key,
            'locale' => $locale,
        ]);
    }
}

