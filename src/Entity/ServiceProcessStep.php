<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ServiceProcessStepRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @brief Ordered process step label for a service detail page (FR/DE).
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
#[ORM\Entity(repositoryClass: ServiceProcessStepRepository::class)]
#[ORM\Table(name: 'service_process_step')]
#[ORM\Index(name: 'idx_service_process_step_service_position', columns: ['service_id', 'position'])]
class ServiceProcessStep
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Service::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Service $service = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $position = 0;

    #[ORM\Column(name: 'label_fr', length: 500)]
    private string $labelFr = '';

    #[ORM\Column(name: 'label_de', length: 500)]
    private string $labelDe = '';

    /**
     * @brief Returns the identifier.
     *
     * @return int|null The identifier or null if not persisted.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @brief Returns the parent service.
     *
     * @return Service|null The service.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getService(): ?Service
    {
        return $this->service;
    }

    /**
     * @brief Sets the parent service.
     *
     * @param Service|null $service The service.
     * @return self Fluent interface.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @brief Gets the display order position.
     *
     * @return int The position.
     * @date 2026-03-22
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
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @brief Gets the French label.
     *
     * @return string The label.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getLabelFr(): string
    {
        return $this->labelFr;
    }

    /**
     * @brief Sets the French label.
     *
     * @param string $labelFr The label.
     * @return self Fluent interface.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function setLabelFr(string $labelFr): self
    {
        $this->labelFr = $labelFr;

        return $this;
    }

    /**
     * @brief Gets the German label.
     *
     * @return string The label.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getLabelDe(): string
    {
        return $this->labelDe;
    }

    /**
     * @brief Sets the German label.
     *
     * @param string $labelDe The label.
     * @return self Fluent interface.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function setLabelDe(string $labelDe): self
    {
        $this->labelDe = $labelDe;

        return $this;
    }
}
