<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * DTO for quote request form data.
 *
 * @author Stephane H.
 * @created 2026-03-11
 */
class DevisRequest
{
    public ?string $nom = null;
    public ?string $email = null;
    public ?string $telephone = null;
    public ?string $vehicule = null;
    public ?int $anneeVehicule = null;
    public ?string $typeCarburant = null;
    public ?string $typePrestation = null;
    public ?string $message = null;
    /** @var UploadedFile[] */
    public array $photos = [];
    public ?string $website = null; // honeypot
}
