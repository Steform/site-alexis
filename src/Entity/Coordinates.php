<?php

namespace App\Entity;

use App\Repository\CoordinatesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @brief Stores main business contact coordinates.
 *
 * @date 2026-03-16
 * @author Stephane H.
 */
#[ORM\Entity(repositoryClass: CoordinatesRepository::class)]
class Coordinates
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $companyName = null;

    #[ORM\Column(length: 255)]
    private ?string $street = null;

    #[ORM\Column(length: 20)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 50)]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $googleMapsEmbedUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facebookUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $instagramUrl = null;

    /**
     * @brief Returns the identifier.
     *
     * @return int|null The identifier or null if not persisted.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @brief Gets the company name.
     *
     * @return string|null The company name.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    /**
     * @brief Sets the company name.
     *
     * @param string $companyName The company name.
     * @return self Fluent interface.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * @brief Gets the street.
     *
     * @return string|null The street.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @brief Sets the street.
     *
     * @param string $street The street.
     * @return self Fluent interface.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    /**
     * @brief Gets the postal code.
     *
     * @return string|null The postal code.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @brief Sets the postal code.
     *
     * @param string $postalCode The postal code.
     * @return self Fluent interface.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @brief Gets the city.
     *
     * @return string|null The city name.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @brief Sets the city.
     *
     * @param string $city The city name.
     * @return self Fluent interface.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @brief Gets the phone number.
     *
     * @return string|null The phone number.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @brief Sets the phone number.
     *
     * @param string $phone The phone number.
     * @return self Fluent interface.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @brief Gets the email address.
     *
     * @return string|null The email address.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @brief Sets the email address.
     *
     * @param string $email The email address.
     * @return self Fluent interface.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @brief Gets the Google Maps embed URL.
     *
     * @return string|null The Google Maps iframe URL.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function getGoogleMapsEmbedUrl(): ?string
    {
        return $this->googleMapsEmbedUrl;
    }

    /**
     * @brief Sets the Google Maps embed URL.
     *
     * @param string|null $googleMapsEmbedUrl The Google Maps iframe URL.
     * @return self Fluent interface.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function setGoogleMapsEmbedUrl(?string $googleMapsEmbedUrl): self
    {
        $this->googleMapsEmbedUrl = $googleMapsEmbedUrl;

        return $this;
    }

    /**
     * @brief Gets the Facebook page URL.
     *
     * @return string|null The Facebook URL.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function getFacebookUrl(): ?string
    {
        return $this->facebookUrl;
    }

    /**
     * @brief Sets the Facebook page URL.
     *
     * @param string|null $facebookUrl The Facebook URL.
     * @return self Fluent interface.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function setFacebookUrl(?string $facebookUrl): self
    {
        $this->facebookUrl = $facebookUrl;

        return $this;
    }

    /**
     * @brief Gets the Instagram profile URL.
     *
     * @return string|null The Instagram URL.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function getInstagramUrl(): ?string
    {
        return $this->instagramUrl;
    }

    /**
     * @brief Sets the Instagram profile URL.
     *
     * @param string|null $instagramUrl The Instagram URL.
     * @return self Fluent interface.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function setInstagramUrl(?string $instagramUrl): self
    {
        $this->instagramUrl = $instagramUrl;

        return $this;
    }
}

