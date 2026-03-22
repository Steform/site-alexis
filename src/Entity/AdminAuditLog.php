<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AdminAuditLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @brief Generic back-office audit row: action code and JSON payload (non-sensitive).
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
#[ORM\Entity(repositoryClass: AdminAuditLogRepository::class)]
#[ORM\Table(name: 'admin_audit_log')]
#[ORM\Index(name: 'idx_admin_audit_log_created_at', columns: ['created_at'])]
#[ORM\Index(name: 'idx_admin_audit_log_action', columns: ['action'])]
class AdminAuditLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 96)]
    private string $action = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $payload = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?User $createdBy = null;

    /**
     * @brief Returns the identifier.
     *
     * @return int|null The identifier.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @brief Gets the action code (e.g. horaires.update).
     *
     * @return string The action.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @brief Sets the action code.
     *
     * @param string $action The action.
     * @return self Fluent interface.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @brief Gets JSON payload string.
     *
     * @return string|null The payload.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getPayload(): ?string
    {
        return $this->payload;
    }

    /**
     * @brief Sets JSON payload string.
     *
     * @param string|null $payload The payload.
     * @return self Fluent interface.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function setPayload(?string $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @brief Gets creation time.
     *
     * @return \DateTimeImmutable|null The time.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @brief Sets creation time.
     *
     * @param \DateTimeImmutable $createdAt The time.
     * @return self Fluent interface.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @brief Gets the acting user.
     *
     * @return User|null The user.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * @brief Sets the acting user.
     *
     * @param UserInterface|null $createdBy The acting user.
     * @return self Fluent interface.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function setCreatedBy(?UserInterface $createdBy): self
    {
        $this->createdBy = $createdBy instanceof User ? $createdBy : null;

        return $this;
    }
}
