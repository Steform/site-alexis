<?php

namespace App\Entity;

use App\Repository\HomeHeroPhotoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @brief Home hero slider photo entity.
 *
 * @date 2026-03-19
 * @author Stephane H.
 */
#[ORM\Entity(repositoryClass: HomeHeroPhotoRepository::class)]
#[ORM\Table(name: 'home_hero_photo')]
class HomeHeroPhoto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $altFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $altDe = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $position = 0;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isActive = true;

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
     * @brief Gets the image path.
     *
     * @return string|null The relative image path.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @brief Sets the image path.
     *
     * @param string $image The relative image path.
     * @return self Fluent interface.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @brief Gets the French alt text.
     *
     * @return string|null The French alt text.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getAltFr(): ?string
    {
        return $this->altFr;
    }

    /**
     * @brief Sets the French alt text.
     *
     * @param string|null $altFr The French alt text.
     * @return self Fluent interface.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function setAltFr(?string $altFr): self
    {
        $this->altFr = $altFr;

        return $this;
    }

    /**
     * @brief Gets the German alt text.
     *
     * @return string|null The German alt text.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getAltDe(): ?string
    {
        return $this->altDe;
    }

    /**
     * @brief Sets the German alt text.
     *
     * @param string|null $altDe The German alt text.
     * @return self Fluent interface.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function setAltDe(?string $altDe): self
    {
        $this->altDe = $altDe;

        return $this;
    }

    /**
     * @brief Gets the display order position.
     *
     * @return int The position.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @brief Sets the display order position.
     *
     * @param int $position The position.
     * @return self Fluent interface.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @brief Checks whether photo is active.
     *
     * @return bool True if active.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @brief Sets active status.
     *
     * @param bool $isActive The active status.
     * @return self Fluent interface.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}

