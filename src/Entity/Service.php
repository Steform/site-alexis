<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Service entity (car body shop services displayed on public pages).
 *
 * @author Stephane H.
 * @date 2026-03-19
 */
#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ORM\Table(name: 'service')]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $slugDe = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titreDe = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionDe = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    /**
     * Optional wide hero image for the public service detail page (list vignette uses `image`).
     */
    #[ORM\Column(name: 'detail_hero_image', length: 500, nullable: true)]
    private ?string $detailHeroImage = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private ?int $ordre = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlugDe(): ?string
    {
        return $this->slugDe;
    }

    public function setSlugDe(?string $slugDe): static
    {
        $this->slugDe = $slugDe;

        return $this;
    }

    /**
     * Returns the slug for the given locale (slug for fr, slugDe for de, fallback to slug).
     */
    public function getSlugForLocale(string $locale): string
    {
        return ($locale === 'de' && $this->slugDe !== null) ? $this->slugDe : (string) $this->slug;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getTitreDe(): ?string
    {
        return $this->titreDe;
    }

    public function setTitreDe(?string $titreDe): static
    {
        $this->titreDe = $titreDe;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDescriptionDe(): ?string
    {
        return $this->descriptionDe;
    }

    public function setDescriptionDe(?string $descriptionDe): static
    {
        $this->descriptionDe = $descriptionDe;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @brief Returns the optional public detail-page hero image path (relative to public/).
     *
     * @return string|null The path or null when using list image as fallback.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getDetailHeroImage(): ?string
    {
        return $this->detailHeroImage;
    }

    /**
     * @brief Sets the optional public detail-page hero image path.
     *
     * @param string|null $detailHeroImage The relative path or null to clear.
     * @return $this
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function setDetailHeroImage(?string $detailHeroImage): static
    {
        $this->detailHeroImage = $detailHeroImage;

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
}
