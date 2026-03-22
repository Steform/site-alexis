<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\EntitySnapshotHistory;
use App\Repository\EntitySnapshotHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @brief Restores entity state from an EntitySnapshotHistory row.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
final class EntitySnapshotRollbackService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EntitySnapshotHistoryRepository $entitySnapshotHistoryRepository,
        private readonly EntitySnapshotStateSerializer $serializer,
    ) {
    }

    /**
     * @brief Rolls back a change by applying the stored snapshot (inverse of change kind).
     *
     * @param int $historyId The entity_snapshot_history.id.
     * @param UserInterface|null $actor The user (unused; reserved for future audit).
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function rollback(int $historyId, ?UserInterface $actor = null): void
    {
        $row = $this->entitySnapshotHistoryRepository->find($historyId);
        if ($row === null) {
            throw new \InvalidArgumentException('Snapshot history entry not found.');
        }

        /** @var array<string, mixed> $data */
        $data = json_decode($row->getSnapshotJson(), true, 512, \JSON_THROW_ON_ERROR);
        $class = $row->getEntityClass();

        match ($row->getChangeKind()) {
            EntitySnapshotHistory::CHANGE_UPDATE => $this->rollbackUpdate($class, $data),
            EntitySnapshotHistory::CHANGE_DELETE => $this->rollbackDelete($class, $data),
            EntitySnapshotHistory::CHANGE_CREATE => $this->rollbackCreate($class, $data),
            default => throw new \InvalidArgumentException('Unknown change kind.'),
        };

        $this->em->flush();
    }

    /**
     * @brief Restores field values from snapshot (undo update).
     *
     * @param class-string $class The entity class.
     * @param array<string, mixed> $data The snapshot data.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function rollbackUpdate(string $class, array $data): void
    {
        $meta = $this->em->getClassMetadata($class);
        $idField = $meta->getSingleIdentifierFieldName();
        $id = $data[$idField] ?? null;
        if ($id === null) {
            throw new \InvalidArgumentException('Snapshot missing identifier.');
        }
        $entity = $this->em->find($class, $id);
        if ($entity === null) {
            throw new \InvalidArgumentException('Entity no longer exists; cannot restore.');
        }
        $this->serializer->applyDataToEntity($this->em, $entity, $data);
    }

    /**
     * @brief Recreates a deleted entity (undo delete).
     *
     * @param class-string $class The entity class.
     * @param array<string, mixed> $data The snapshot data.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function rollbackDelete(string $class, array $data): void
    {
        $entity = $this->serializer->createEntityFromData($this->em, $class, $data);
        $this->em->persist($entity);
    }

    /**
     * @brief Removes a created entity (undo create).
     *
     * @param class-string $class The entity class.
     * @param array<string, mixed> $data The snapshot data.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function rollbackCreate(string $class, array $data): void
    {
        $meta = $this->em->getClassMetadata($class);
        $idField = $meta->getSingleIdentifierFieldName();
        $id = $data[$idField] ?? null;
        if ($id === null) {
            throw new \InvalidArgumentException('Snapshot missing identifier.');
        }
        $entity = $this->em->find($class, $id);
        if ($entity === null) {
            return;
        }
        $this->em->remove($entity);
    }
}
