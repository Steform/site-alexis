<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @brief Normalized row for merged CMS history timeline (blocks, uploads, audit).
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
final class HistoryTimelineItem
{
    /**
     * @brief Creates a timeline item.
     *
     * @param string $kind One of HistoryMergeService::KIND_*.
     * @param \DateTimeImmutable $occurredAt Event time.
     * @param string $typeTranslationKey back.* key for the row type label.
     * @param string $summary Short summary (plain text).
     * @param string $detail Longer detail (plain text or truncated JSON).
     * @param string|null $userDisplayName Display name for actor.
     * @param int|null $rollbackBlockHistoryId Content block history id for rollback, if any.
     * @param int $sourceId Original row id (block, upload, or audit) for stable ordering.
     * @param string $filterKind Filter bucket: cms_block, upload, audit.
     * @param string $filterDomain Domain key for accordion filters (e.g. horaires, upload:about).
     * @param string|null $blockColorHex Light theme color for CMS block rows.
     * @param string|null $blockColorDarkHex Dark theme color for CMS block rows.
     * @param int|null $rollbackEntitySnapshotId Entity snapshot history id for rollback, if any.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function __construct(
        public readonly string $kind,
        public readonly \DateTimeImmutable $occurredAt,
        public readonly string $typeTranslationKey,
        public readonly string $summary,
        public readonly string $detail,
        public readonly ?string $userDisplayName,
        public readonly ?int $rollbackBlockHistoryId,
        public readonly int $sourceId,
        public readonly string $filterKind,
        public readonly string $filterDomain,
        public readonly ?string $blockColorHex = null,
        public readonly ?string $blockColorDarkHex = null,
        public readonly ?int $rollbackEntitySnapshotId = null,
    ) {
    }
}
