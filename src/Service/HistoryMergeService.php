<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\AdminAuditLog;
use App\Entity\ContentBlockHistory;
use App\Entity\EntitySnapshotHistory;
use App\Entity\UploadDeletionHistory;
use App\Entity\User;
use App\Model\HistoryTimelineItem;
use App\Repository\AdminAuditLogRepository;
use App\Repository\ContentBlockHistoryRepository;
use App\Repository\EntitySnapshotHistoryRepository;
use App\Repository\UploadDeletionHistoryRepository;

/**
 * @brief Merges content block history, upload archive history, and admin audit into one timeline.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
class HistoryMergeService
{
    public const KIND_BLOCK = 'block';

    public const KIND_UPLOAD = 'upload';

    public const KIND_AUDIT = 'audit';

    public const KIND_ENTITY_SNAPSHOT = 'entity_snapshot';

    public const FILTER_KIND_CMS_BLOCK = 'cms_block';

    public const FILTER_KIND_UPLOAD = 'upload';

    public const FILTER_KIND_AUDIT = 'audit';

    public const FILTER_KIND_ENTITY_SNAPSHOT = 'entity_snapshot';

    private const PER_SOURCE_LIMIT = 350;

    private const MERGED_LIMIT = 600;

    /**
     * @brief Creates the merge service.
     *
     * @param ContentBlockHistoryRepository $contentBlockHistoryRepository The block history repository.
     * @param UploadDeletionHistoryRepository $uploadDeletionHistoryRepository The upload history repository.
     * @param AdminAuditLogRepository $adminAuditLogRepository The audit log repository.
     * @param EntitySnapshotHistoryRepository $entitySnapshotHistoryRepository The entity snapshot repository.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function __construct(
        private readonly ContentBlockHistoryRepository $contentBlockHistoryRepository,
        private readonly UploadDeletionHistoryRepository $uploadDeletionHistoryRepository,
        private readonly AdminAuditLogRepository $adminAuditLogRepository,
        private readonly EntitySnapshotHistoryRepository $entitySnapshotHistoryRepository,
    ) {
    }

    /**
     * @brief Builds merged, sorted timeline with optional filters (GET query).
     *
     * @param array<string, mixed> $query Query parameters: page, locale, kinds[], domains[].
     * @return HistoryTimelineItem[] Newest first.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function buildMergedTimeline(array $query): array
    {
        $pageFilter = isset($query['page']) && \is_string($query['page']) && $query['page'] !== ''
            ? $query['page'] : null;
        $localeFilter = isset($query['locale']) && \is_string($query['locale']) && $query['locale'] !== ''
            ? $query['locale'] : null;

        $kindFilters = $this->parseStringList($query['kinds'] ?? null);
        $domainFilters = $this->parseStringList($query['domains'] ?? null);

        $blocks = $this->contentBlockHistoryRepository->findAllOrdered(self::PER_SOURCE_LIMIT, $pageFilter, $localeFilter);
        $uploads = $this->uploadDeletionHistoryRepository->findAllOrdered(self::PER_SOURCE_LIMIT);
        $audits = $this->adminAuditLogRepository->findAllOrdered(self::PER_SOURCE_LIMIT);
        $snapshots = $this->entitySnapshotHistoryRepository->findAllOrdered(self::PER_SOURCE_LIMIT);

        $items = [];
        foreach ($blocks as $h) {
            $items[] = $this->mapBlock($h);
        }
        foreach ($uploads as $u) {
            $items[] = $this->mapUpload($u);
        }
        foreach ($audits as $a) {
            $items[] = $this->mapAudit($a);
        }
        foreach ($snapshots as $s) {
            $items[] = $this->mapEntitySnapshot($s);
        }

        $items = $this->applyFilters($items, $kindFilters, $domainFilters);

        usort($items, static function (HistoryTimelineItem $a, HistoryTimelineItem $b): int {
            $t = $b->occurredAt <=> $a->occurredAt;
            if ($t !== 0) {
                return $t;
            }

            return $b->sourceId <=> $a->sourceId;
        });

        if (\count($items) > self::MERGED_LIMIT) {
            $items = \array_slice($items, 0, self::MERGED_LIMIT);
        }

        return $items;
    }

    /**
     * @brief Maps a content block history row to a timeline item.
     *
     * @param ContentBlockHistory $h The entity.
     * @return HistoryTimelineItem The DTO.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function mapBlock(ContentBlockHistory $h): HistoryTimelineItem
    {
        $value = (string) ($h->getValue() ?? '');
        $preview = $this->truncateString(strip_tags($value), 120);
        $user = $this->formatUser($h->getCreatedBy());
        $pageName = (string) $h->getPageName();

        return new HistoryTimelineItem(
            self::KIND_BLOCK,
            $h->getCreatedAt() ?? new \DateTimeImmutable(),
            'back.content.history.timeline.type.cms_block',
            $pageName . ' / ' . (string) $h->getBlockKey() . ' (' . strtoupper((string) $h->getLocale()) . ')',
            $preview,
            $user,
            $h->getId(),
            (int) $h->getId(),
            self::FILTER_KIND_CMS_BLOCK,
            'cms:' . $pageName,
            $h->getColor(),
            $h->getColorDark(),
        );
    }

    /**
     * @brief Maps an upload deletion/replacement archive row.
     *
     * @param UploadDeletionHistory $u The entity.
     * @return HistoryTimelineItem The DTO.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function mapUpload(UploadDeletionHistory $u): HistoryTimelineItem
    {
        $meta = $u->getMetadata();
        $reason = 'delete';
        if ($meta !== null && $meta !== '') {
            try {
                $decoded = json_decode($meta, true, 5, \JSON_THROW_ON_ERROR);
                if (\is_array($decoded) && isset($decoded['reason']) && \is_string($decoded['reason'])) {
                    $reason = $decoded['reason'];
                }
            } catch (\JsonException) {
            }
        }

        $summary = $u->getContext() . ' — ' . basename($u->getOriginalRelativePath());
        $detailParts = [];
        if ($u->isFileMissing()) {
            $detailParts[] = 'missing_file';
        } elseif ($u->getArchivedRelativePath()) {
            $detailParts[] = $u->getArchivedRelativePath();
        }
        if ($meta !== null && $meta !== '') {
            $detailParts[] = $this->truncateString($meta, 200);
        }

        $typeKey = \in_array($reason, ['delete', 'replace'], true)
            ? 'back.content.history.timeline.type.upload_' . $reason
            : 'back.content.history.timeline.type.upload_archive';

        return new HistoryTimelineItem(
            self::KIND_UPLOAD,
            $u->getCreatedAt() ?? new \DateTimeImmutable(),
            $typeKey,
            $summary,
            implode(' | ', $detailParts),
            $this->formatUser($u->getCreatedBy()),
            null,
            (int) $u->getId(),
            self::FILTER_KIND_UPLOAD,
            'upload:' . $u->getContext(),
        );
    }

    /**
     * @brief Maps an admin audit row.
     *
     * @param AdminAuditLog $a The entity.
     * @return HistoryTimelineItem The DTO.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function mapAudit(AdminAuditLog $a): HistoryTimelineItem
    {
        $action = $a->getAction();
        $domain = $this->auditDomainKey($action);
        $payload = $a->getPayload() ?? '';
        $detail = $this->truncateString($payload, 400);

        $actionKey = 'back.content.history.timeline.action.' . str_replace('.', '_', $action);

        return new HistoryTimelineItem(
            self::KIND_AUDIT,
            $a->getCreatedAt() ?? new \DateTimeImmutable(),
            $actionKey,
            $action,
            $detail,
            $this->formatUser($a->getCreatedBy()),
            null,
            (int) $a->getId(),
            self::FILTER_KIND_AUDIT,
            $domain,
        );
    }

    /**
     * @brief Maps an entity snapshot history row to a timeline item.
     *
     * @param EntitySnapshotHistory $s The entity.
     * @return HistoryTimelineItem The DTO.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function mapEntitySnapshot(EntitySnapshotHistory $s): HistoryTimelineItem
    {
        $summary = $s->getDomain() . ' — ' . $s->getChangeKind() . ' (id ' . ($s->getEntityId() ?? '?') . ')';
        $detail = $this->truncateString($s->getSnapshotJson(), 220);

        return new HistoryTimelineItem(
            self::KIND_ENTITY_SNAPSHOT,
            $s->getCreatedAt() ?? new \DateTimeImmutable(),
            'back.content.history.timeline.type.entity_snapshot',
            $summary,
            $detail,
            $this->formatUser($s->getCreatedBy()),
            null,
            (int) $s->getId(),
            self::FILTER_KIND_ENTITY_SNAPSHOT,
            'entity:' . $s->getDomain(),
            null,
            null,
            (int) $s->getId(),
        );
    }

    /**
     * @brief Derives filter domain key from audit action code.
     *
     * @param string $action The action.
     * @return string The domain key.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function auditDomainKey(string $action): string
    {
        $pos = strpos($action, '.');
        if ($pos === false) {
            return 'audit:' . $action;
        }

        return 'audit:' . substr($action, 0, $pos);
    }

    /**
     * @brief Applies kind and domain filters.
     *
     * @param HistoryTimelineItem[] $items All items.
     * @param string[] $kindFilters Selected kind filter values (empty = no filter).
     * @param string[] $domainFilters Selected domain keys (empty = no filter).
     * @return HistoryTimelineItem[] Filtered items.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function applyFilters(array $items, array $kindFilters, array $domainFilters): array
    {
        if ($kindFilters === [] && $domainFilters === []) {
            return $items;
        }

        $out = [];
        foreach ($items as $item) {
            if ($kindFilters !== [] && !\in_array($item->filterKind, $kindFilters, true)) {
                continue;
            }
            if ($domainFilters !== [] && !$this->matchesAnyDomainFilter($item->filterDomain, $domainFilters)) {
                continue;
            }
            $out[] = $item;
        }

        return $out;
    }

    /**
     * @brief Returns true when the item domain matches any selected filter (exact or cms service prefix).
     *
     * @param string $itemDomain The item filter domain.
     * @param string[] $domainFilters Selected filter values.
     * @return bool True when matched.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function matchesAnyDomainFilter(string $itemDomain, array $domainFilters): bool
    {
        foreach ($domainFilters as $filter) {
            if ($itemDomain === $filter) {
                return true;
            }
            if ($filter === 'cms:service_' && str_starts_with($itemDomain, 'cms:service_')) {
                return true;
            }
            if ($filter === 'entity:' && str_starts_with($itemDomain, 'entity:')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @brief Parses comma-separated or array query values.
     *
     * @param mixed $raw Raw query value.
     * @return string[] Non-empty strings.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function parseStringList(mixed $raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }
        if (\is_array($raw)) {
            $parts = $raw;
        } else {
            $parts = explode(',', (string) $raw);
        }
        $out = [];
        foreach ($parts as $p) {
            $p = trim((string) $p);
            if ($p !== '') {
                $out[] = $p;
            }
        }

        return array_values(array_unique($out));
    }

    /**
     * @brief Truncates a string with ellipsis.
     *
     * @param string $s The string.
     * @param int $max Max length.
     * @return string Truncated string.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function truncateString(string $s, int $max): string
    {
        if (strlen($s) <= $max) {
            return $s;
        }

        return substr($s, 0, $max) . '…';
    }

    /**
     * @brief Formats a user for display.
     *
     * @param User|null $user The user entity.
     * @return string|null Display name or null.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function formatUser(?User $user): ?string
    {
        if ($user === null) {
            return null;
        }
        $nom = $user->getNom();
        if (\is_string($nom) && $nom !== '') {
            return $nom;
        }

        return $user->getEmail();
    }
}
