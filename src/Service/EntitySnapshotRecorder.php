<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\EntitySnapshotHistory;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @brief Records entity snapshots before update/delete and after create for rollback.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
final class EntitySnapshotRecorder
{
    public function __construct(
        private readonly EntitySnapshotStateSerializer $serializer,
    ) {
    }

    /**
     * @brief Persists a snapshot of the entity state before an update (call before flush).
     *
     * @param EntityManagerInterface $em The entity manager.
     * @param object $entity The managed entity scheduled for update.
     * @param string $domain The domain key (see EntitySnapshotDomain).
     * @param UserInterface|null $user The acting user.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function recordBeforeUpdate(EntityManagerInterface $em, object $entity, string $domain, ?UserInterface $user = null): void
    {
        $meta = $em->getClassMetadata($entity::class);
        $uow = $em->getUnitOfWork();
        $uow->computeChangeSets();
        if (!$uow->isInIdentityMap($entity) || !$uow->isScheduledForUpdate($entity)) {
            return;
        }
        $original = $uow->getOriginalEntityData($entity);
        if ($original === []) {
            return;
        }
        $payload = $this->serializer->normalizeOriginalData($original, $meta);
        $idField = $meta->getSingleIdentifierFieldName();
        if (!isset($payload[$idField])) {
            $payload[$idField] = $meta->getFieldValue($entity, $idField);
        }

        $this->persistRow($em, $domain, $entity::class, (int) $payload[$idField], EntitySnapshotHistory::CHANGE_UPDATE, $payload, $user);
    }

    /**
     * @brief Records the current database-backed entity state before an in-place mutation (e.g. image replace, reorder) when UnitOfWork may not yet schedule an update.
     *
     * @param EntityManagerInterface $em The entity manager.
     * @param object $entity The managed entity.
     * @param string $domain The domain key.
     * @param UserInterface|null $user The acting user.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function recordCurrentStateForPendingUpdate(EntityManagerInterface $em, object $entity, string $domain, ?UserInterface $user = null): void
    {
        $meta = $em->getClassMetadata($entity::class);
        $payload = $this->serializer->normalizeEntity($em, $entity);
        $idField = $meta->getSingleIdentifierFieldName();
        $id = (int) ($payload[$idField] ?? 0);
        $this->persistRow($em, $domain, $entity::class, $id, EntitySnapshotHistory::CHANGE_UPDATE, $payload, $user);
    }

    /**
     * @brief Persists a full entity snapshot before delete (call before remove + flush).
     *
     * @param EntityManagerInterface $em The entity manager.
     * @param object $entity The entity to delete.
     * @param string $domain The domain key.
     * @param UserInterface|null $user The acting user.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function recordBeforeDelete(EntityManagerInterface $em, object $entity, string $domain, ?UserInterface $user = null): void
    {
        $payload = $this->serializer->normalizeEntity($em, $entity);
        $idField = $em->getClassMetadata($entity::class)->getSingleIdentifierFieldName();
        $id = (int) ($payload[$idField] ?? 0);
        $this->persistRow($em, $domain, $entity::class, $id, EntitySnapshotHistory::CHANGE_DELETE, $payload, $user);
    }

    /**
     * @brief Persists a snapshot after create (call after flush so id is assigned).
     *
     * @param EntityManagerInterface $em The entity manager.
     * @param object $entity The persisted entity.
     * @param string $domain The domain key.
     * @param UserInterface|null $user The acting user.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function recordAfterCreate(EntityManagerInterface $em, object $entity, string $domain, ?UserInterface $user = null): void
    {
        $payload = $this->serializer->normalizeEntity($em, $entity);
        $idField = $em->getClassMetadata($entity::class)->getSingleIdentifierFieldName();
        $id = (int) ($payload[$idField] ?? 0);
        $this->persistRow($em, $domain, $entity::class, $id, EntitySnapshotHistory::CHANGE_CREATE, $payload, $user);
    }

    /**
     * @brief Persists the history row (does not flush).
     *
     * @param EntityManagerInterface $em The entity manager.
     * @param string $domain The domain.
     * @param class-string $entityClass The entity class.
     * @param int $entityId The entity id.
     * @param string $changeKind One of EntitySnapshotHistory::CHANGE_*.
     * @param array<string, mixed> $payload The snapshot payload.
     * @param UserInterface|null $user The acting user.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function persistRow(
        EntityManagerInterface $em,
        string $domain,
        string $entityClass,
        int $entityId,
        string $changeKind,
        array $payload,
        ?UserInterface $user,
    ): void {
        $row = new EntitySnapshotHistory();
        $row->setDomain($domain);
        $row->setEntityClass($entityClass);
        $row->setEntityId($entityId > 0 ? $entityId : null);
        $row->setChangeKind($changeKind);
        $row->setSnapshotJson(json_encode($payload, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE));
        $row->setCreatedAt(new \DateTimeImmutable());
        if ($user instanceof User) {
            $row->setCreatedBy($user);
        }
        $em->persist($row);
    }
}
