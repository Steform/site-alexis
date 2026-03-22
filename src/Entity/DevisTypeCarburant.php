<?php

namespace App\Entity;

use App\Repository\DevisTypeCarburantRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Quote request fuel type (managed in back-office).
 *
 * @author Stephane H.
 * @date 2026-03-19
 */
#[ORM\Entity(repositoryClass: DevisTypeCarburantRepository::class)]
#[ORM\Table(name: 'devis_type_carburant')]
class DevisTypeCarburant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $labelDe = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private ?int $ordre = 0;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $actif = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabelDe(): ?string
    {
        return $this->labelDe;
    }

    public function setLabelDe(?string $labelDe): static
    {
        $this->labelDe = $labelDe;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): static
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }
}
