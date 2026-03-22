<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UploadDeletionHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @brief Records a back-office image deletion: original path, optional archived file path, metadata.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
#[ORM\Entity(repositoryClass: UploadDeletionHistoryRepository::class)]
#[ORM\Table(name: 'upload_deletion_history')]
#[ORM\Index(name: 'idx_upload_deletion_history_created_at', columns: ['created_at'])]
class UploadDeletionHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @brief Logical source: about, home-hero, or gallery (matches archive subdirectory).
     */
    #[ORM\Column(length: 32)]
    private string $context = '';

    #[ORM\Column(length: 512)]
    private string $originalRelativePath = '';

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $archivedRelativePath = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $fileMissing = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $metadata = null;

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
     * @brief Gets the archive context (about, home-hero, gallery).
     *
     * @return string The context.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getContext(): string
    {
        return $this->context;
    }

    /**
     * @brief Sets the archive context.
     *
     * @param string $context The context.
     * @return self Fluent interface.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @brief Gets the original public-relative path before deletion.
     *
     * @return string The path.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getOriginalRelativePath(): string
    {
        return $this->originalRelativePath;
    }

    /**
     * @brief Sets the original relative path.
     *
     * @param string $originalRelativePath The path.
     * @return self Fluent interface.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function setOriginalRelativePath(string $originalRelativePath): self
    {
        $this->originalRelativePath = $originalRelativePath;

        return $this;
    }

    /**
     * @brief Gets the path under public/ after move to uploads/historique/... or null if missing.
     *
     * @return string|null The archived relative path.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getArchivedRelativePath(): ?string
    {
        return $this->archivedRelativePath;
    }

    /**
     * @brief Sets the archived relative path.
     *
     * @param string|null $archivedRelativePath The path.
     * @return self Fluent interface.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function setArchivedRelativePath(?string $archivedRelativePath): self
    {
        $this->archivedRelativePath = $archivedRelativePath;

        return $this;
    }

    /**
     * @brief Whether the source file was not found on disk.
     *
     * @return bool True if missing.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function isFileMissing(): bool
    {
        return $this->fileMissing;
    }

    /**
     * @brief Sets the file missing flag.
     *
     * @param bool $fileMissing The flag.
     * @return self Fluent interface.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function setFileMissing(bool $fileMissing): self
    {
        $this->fileMissing = $fileMissing;

        return $this;
    }

    /**
     * @brief Gets optional JSON metadata (alt text, titles, entity id).
     *
     * @return string|null The metadata.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getMetadata(): ?string
    {
        return $this->metadata;
    }

    /**
     * @brief Sets optional JSON metadata.
     *
     * @param string|null $metadata The metadata.
     * @return self Fluent interface.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function setMetadata(?string $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @brief Gets the creation time.
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
     * @brief Sets the creation time.
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
     * @brief Gets the user who deleted the image.
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
     * @brief Sets the user who deleted the image.
     *
     * @param UserInterface|null $createdBy The user.
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
