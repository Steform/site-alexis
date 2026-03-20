<?php

namespace App\Entity;

use App\Repository\ContentBlockHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @brief Stores a snapshot of a content block state before it was modified.
 *
 * Each entry represents the previous state of a block (value, color, color_dark)
 * before a save. Used for history display and rollback.
 *
 * @date 2026-03-19
 * @author Stephane H.
 */
#[ORM\Entity(repositoryClass: ContentBlockHistoryRepository::class)]
#[ORM\Table(name: 'content_block_history')]
#[ORM\Index(name: 'idx_content_block_history_lookup', columns: ['page_name', 'block_key', 'locale', 'created_at'])]
class ContentBlockHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $pageName = null;

    #[ORM\Column(name: 'block_key', length: 150)]
    private ?string $blockKey = null;

    #[ORM\Column(length: 5)]
    private ?string $locale = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $value = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(name: 'color_dark', length: 7, nullable: true)]
    private ?string $colorDark = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?User $createdBy = null;

    /**
     * @brief Returns the identifier.
     *
     * @return int|null The identifier.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @brief Gets the page name.
     *
     * @return string|null The page name.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getPageName(): ?string
    {
        return $this->pageName;
    }

    /**
     * @brief Sets the page name.
     *
     * @param string $pageName The page name.
     * @return self Fluent interface.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function setPageName(string $pageName): self
    {
        $this->pageName = $pageName;

        return $this;
    }

    /**
     * @brief Gets the block key.
     *
     * @return string|null The block key.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getBlockKey(): ?string
    {
        return $this->blockKey;
    }

    /**
     * @brief Sets the block key.
     *
     * @param string $blockKey The block key.
     * @return self Fluent interface.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function setBlockKey(string $blockKey): self
    {
        $this->blockKey = $blockKey;

        return $this;
    }

    /**
     * @brief Gets the locale.
     *
     * @return string|null The locale.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @brief Sets the locale.
     *
     * @param string $locale The locale.
     * @return self Fluent interface.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @brief Gets the content value.
     *
     * @return string|null The content value.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @brief Sets the content value.
     *
     * @param string $value The content value.
     * @return self Fluent interface.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @brief Gets the light mode color.
     *
     * @return string|null The HEX color.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @brief Sets the light mode color.
     *
     * @param string|null $color The HEX color.
     * @return self Fluent interface.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @brief Gets the dark mode color.
     *
     * @return string|null The HEX color.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getColorDark(): ?string
    {
        return $this->colorDark;
    }

    /**
     * @brief Sets the dark mode color.
     *
     * @param string|null $colorDark The HEX color.
     * @return self Fluent interface.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function setColorDark(?string $colorDark): self
    {
        $this->colorDark = $colorDark;

        return $this;
    }

    /**
     * @brief Gets the creation date.
     *
     * @return \DateTimeImmutable|null The creation date.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @brief Sets the creation date.
     *
     * @param \DateTimeImmutable $createdAt The creation date.
     * @return self Fluent interface.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @brief Gets the user who triggered the save that replaced this state.
     *
     * @return User|null The user.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * @brief Sets the user who triggered the save.
     *
     * @param UserInterface|null $createdBy The user.
     * @return self Fluent interface.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function setCreatedBy(?UserInterface $createdBy): self
    {
        $this->createdBy = $createdBy instanceof User ? $createdBy : null;

        return $this;
    }
}
