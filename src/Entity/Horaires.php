<?php

namespace App\Entity;

use App\Repository\HorairesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Opening hours entity (one row per day).
 *
 * @author Stephane H.
 * @created 2026-03-11
 *
 * @inputs  None
 * @outputs Horaires entity with jour, heure_debut, heure_fin
 */
#[ORM\Entity(repositoryClass: HorairesRepository::class)]
#[ORM\Table(name: 'horaires')]
class Horaires
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $jour = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $heureDebut = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $heureFin = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $heureDebutMatin = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $heureFinMatin = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $heureDebutApresMidi = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $heureFinApresMidi = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaireDe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJour(): ?string
    {
        return $this->jour;
    }

    public function setJour(string $jour): static
    {
        $this->jour = $jour;

        return $this;
    }

    public function getHeureDebut(): ?\DateTimeInterface
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(?\DateTimeInterface $heureDebut): static
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getHeureFin(): ?\DateTimeInterface
    {
        return $this->heureFin;
    }

    public function setHeureFin(?\DateTimeInterface $heureFin): static
    {
        $this->heureFin = $heureFin;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getHeureDebutMatin(): ?\DateTimeInterface
    {
        return $this->heureDebutMatin;
    }

    public function setHeureDebutMatin(?\DateTimeInterface $heureDebutMatin): static
    {
        $this->heureDebutMatin = $heureDebutMatin;

        return $this;
    }

    public function getHeureFinMatin(): ?\DateTimeInterface
    {
        return $this->heureFinMatin;
    }

    public function setHeureFinMatin(?\DateTimeInterface $heureFinMatin): static
    {
        $this->heureFinMatin = $heureFinMatin;

        return $this;
    }

    public function getHeureDebutApresMidi(): ?\DateTimeInterface
    {
        return $this->heureDebutApresMidi;
    }

    public function setHeureDebutApresMidi(?\DateTimeInterface $heureDebutApresMidi): static
    {
        $this->heureDebutApresMidi = $heureDebutApresMidi;

        return $this;
    }

    public function getHeureFinApresMidi(): ?\DateTimeInterface
    {
        return $this->heureFinApresMidi;
    }

    public function setHeureFinApresMidi(?\DateTimeInterface $heureFinApresMidi): static
    {
        $this->heureFinApresMidi = $heureFinApresMidi;

        return $this;
    }

    public function getCommentaireDe(): ?string
    {
        return $this->commentaireDe;
    }

    public function setCommentaireDe(?string $commentaireDe): static
    {
        $this->commentaireDe = $commentaireDe;

        return $this;
    }
}
