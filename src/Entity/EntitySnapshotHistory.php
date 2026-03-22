<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EntitySnapshotHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @brief Stores a JSON snapshot of an entity state for rollback (before update/delete, after create).
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
#[ORM\Entity(repositoryClass: EntitySnapshotHistoryRepository::class)]
#[ORM\Table(name: 'entity_snapshot_history')]
#[ORM\Index(name: 'idx_entity_snapshot_domain_created', columns: ['domain', 'created_at'])]
#[ORM\Index(name: 'idx_entity_snapshot_entity', columns: ['domain', 'entity_id'])]
class EntitySnapshotHistory
{
    public const CHANGE_UPDATE = 'update';

    public const CHANGE_DELETE = 'delete';

    public const CHANGE_CREATE = 'create';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private string $domain = '';

    #[ORM\Column(length: 255)]
    private string $entityClass = '';

    #[ORM\Column(nullable: true)]
    private ?int $entityId = null;

    /**
     * What change happened after this snapshot: update, delete, or create (snapshot taken after insert).
     */
    #[ORM\Column(length: 16)]
    private string $changeKind = '';

    #[ORM\Column(type: Types::TEXT)]
    private string $snapshotJson = '';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?User $createdBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function setEntityClass(string $entityClass): self
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(?int $entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getChangeKind(): string
    {
        return $this->changeKind;
    }

    public function setChangeKind(string $changeKind): self
    {
        $this->changeKind = $changeKind;

        return $this;
    }

    public function getSnapshotJson(): string
    {
        return $this->snapshotJson;
    }

    public function setSnapshotJson(string $snapshotJson): self
    {
        $this->snapshotJson = $snapshotJson;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
