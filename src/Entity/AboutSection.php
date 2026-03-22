<?php

namespace App\Entity;

use App\Repository\AboutSectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @brief About section singleton entity.
 *
 * @date 2026-03-18
 * @author Stephane H.
 */
#[ORM\Entity(repositoryClass: AboutSectionRepository::class)]
#[ORM\Table(name: 'about_section')]
class AboutSection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $leadFr = null;

    #[ORM\Column(length: 255)]
    private ?string $leadDe = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contentFr = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contentDe = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, AboutPhoto>
     */
    #[ORM\OneToMany(mappedBy: 'aboutSection', targetEntity: AboutPhoto::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $photos;

    /**
     * @brief AboutSection constructor.
     *
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @brief Returns the identifier.
     *
     * @return int|null The identifier or null if not persisted.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @brief Gets the French lead text.
     *
     * @return string|null The French lead text.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function getLeadFr(): ?string
    {
        return $this->leadFr;
    }

    /**
     * @brief Sets the French lead text.
     *
     * @param string $leadFr The French lead text.
     * @return self Fluent interface.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function setLeadFr(string $leadFr): self
    {
        $this->leadFr = $leadFr;

        return $this;
    }

    /**
     * @brief Gets the German lead text.
     *
     * @return string|null The German lead text.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function getLeadDe(): ?string
    {
        return $this->leadDe;
    }

    /**
     * @brief Sets the German lead text.
     *
     * @param string $leadDe The German lead text.
     * @return self Fluent interface.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function setLeadDe(string $leadDe): self
    {
        $this->leadDe = $leadDe;

        return $this;
    }

    /**
     * @brief Gets the French rich content (HTML).
     *
     * @return string|null The French content HTML.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function getContentFr(): ?string
    {
        return $this->contentFr;
    }

    /**
     * @brief Sets the French rich content (HTML).
     *
     * @param string $contentFr The French content HTML.
     * @return self Fluent interface.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function setContentFr(string $contentFr): self
    {
        $this->contentFr = $contentFr;

        return $this;
    }

    /**
     * @brief Gets the German rich content (HTML).
     *
     * @return string|null The German content HTML.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function getContentDe(): ?string
    {
        return $this->contentDe;
    }

    /**
     * @brief Sets the German rich content (HTML).
     *
     * @param string $contentDe The German content HTML.
     * @return self Fluent interface.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function setContentDe(string $contentDe): self
    {
        $this->contentDe = $contentDe;

        return $this;
    }

    /**
     * @brief Gets the last update timestamp.
     *
     * @return \DateTimeImmutable|null The update timestamp.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @brief Sets the last update timestamp.
     *
     * @param \DateTimeImmutable $updatedAt The update timestamp.
     * @return self Fluent interface.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @brief Gets all photos related to this section.
     *
     * @return Collection<int, AboutPhoto> The photos collection.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    /**
     * @brief Adds a photo to the section.
     *
     * @param AboutPhoto $photo The photo to add.
     * @return self Fluent interface.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function addPhoto(AboutPhoto $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setAboutSection($this);
        }

        return $this;
    }

    /**
     * @brief Removes a photo from the section.
     *
     * @param AboutPhoto $photo The photo to remove.
     * @return self Fluent interface.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function removePhoto(AboutPhoto $photo): self
    {
        if ($this->photos->removeElement($photo)) {
            if ($photo->getAboutSection() === $this) {
                $photo->setAboutSection(null);
            }
        }

        return $this;
    }
}

