<?php

namespace App\Entity;

use App\Repository\ServicesWhyCardRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @brief Services page "Why choose us" card entity.
 *
 * @date 2026-03-21
 * @author Stephane H.
 */
#[ORM\Entity(repositoryClass: ServicesWhyCardRepository::class)]
#[ORM\Table(name: 'services_why_card')]
class ServicesWhyCard
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $position = 0;

    #[ORM\Column(length: 255)]
    private ?string $titleFr = null;

    #[ORM\Column(length: 255)]
    private ?string $titleDe = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $textFr = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $textDe = null;

    /**
     * @brief Returns the identifier.
     *
     * @return int|null The identifier or null if not persisted.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @brief Gets the display order position.
     *
     * @return int The position.
     * @date 2026-03-21
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
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @brief Gets the French title.
     *
     * @return string|null The French title.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function getTitleFr(): ?string
    {
        return $this->titleFr;
    }

    /**
     * @brief Sets the French title.
     *
     * @param string|null $titleFr The French title.
     * @return self Fluent interface.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function setTitleFr(?string $titleFr): self
    {
        $this->titleFr = $titleFr;

        return $this;
    }

    /**
     * @brief Gets the German title.
     *
     * @return string|null The German title.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function getTitleDe(): ?string
    {
        return $this->titleDe;
    }

    /**
     * @brief Sets the German title.
     *
     * @param string|null $titleDe The German title.
     * @return self Fluent interface.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function setTitleDe(?string $titleDe): self
    {
        $this->titleDe = $titleDe;

        return $this;
    }

    /**
     * @brief Gets the French text.
     *
     * @return string|null The French text.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function getTextFr(): ?string
    {
        return $this->textFr;
    }

    /**
     * @brief Sets the French text.
     *
     * @param string|null $textFr The French text.
     * @return self Fluent interface.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function setTextFr(?string $textFr): self
    {
        $this->textFr = $textFr;

        return $this;
    }

    /**
     * @brief Gets the German text.
     *
     * @return string|null The German text.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function getTextDe(): ?string
    {
        return $this->textDe;
    }

    /**
     * @brief Sets the German text.
     *
     * @param string|null $textDe The German text.
     * @return self Fluent interface.
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function setTextDe(?string $textDe): self
    {
        $this->textDe = $textDe;

        return $this;
    }
}
