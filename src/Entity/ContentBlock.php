<?php

namespace App\Entity;

use App\Repository\ContentBlockRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @brief Stores editable content blocks per page, key, and locale.
 *
 * @date 2026-03-18
 * @author Stephane H.
 */
#[ORM\Entity(repositoryClass: ContentBlockRepository::class)]
#[ORM\Table(name: 'content_block')]
#[ORM\UniqueConstraint(name: 'uniq_content_block_page_key_locale', columns: ['page_name', 'block_key', 'locale'])]
class ContentBlock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $pageName = null;

    #[ORM\Column(name: 'block_key', length: 150)]
    private ?string $key = null;

    #[ORM\Column(length: 5)]
    private ?string $locale = null;

    #[ORM\Column(length: 20)]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $value = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @brief Returns the identifier.
     *
     * @return int|null The identifier.
     * @date 2026-03-18
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
     * @date 2026-03-18
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
     * @date 2026-03-18
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
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * @brief Sets the block key.
     *
     * @param string $key The block key.
     * @return self Fluent interface.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @brief Gets the locale.
     *
     * @return string|null The locale.
     * @date 2026-03-18
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
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @brief Gets the block type.
     *
     * @return string|null The block type (plain|rich).
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @brief Sets the block type.
     *
     * @param string $type The block type.
     * @return self Fluent interface.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @brief Gets the content value.
     *
     * @return string|null The content value.
     * @date 2026-03-18
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
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @brief Gets the display color for the block.
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
     * @brief Sets the display color for the block.
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
     * @brief Gets the update date.
     *
     * @return \DateTimeImmutable|null The update date.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @brief Sets the update date.
     *
     * @param \DateTimeImmutable $updatedAt The update date.
     * @return self Fluent interface.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}

