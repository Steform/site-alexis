<?php

namespace App\Dto;

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
    public ?string $typePrestation = null;
    public ?string $message = null;
    public ?string $website = null; // honeypot
}
