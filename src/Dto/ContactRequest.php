<?php

namespace App\Dto;

/**
 * @brief DTO for contact form data.
 *
 * @date 2026-03-16
 * @author Stephane H.
 */
class ContactRequest
{
    public ?string $name = null;

    public ?string $email = null;

    public ?string $message = null;

    /**
     * @brief Honeypot field to mitigate bots.
     *
     * @date 2026-03-16
     * @author Stephane H.
     */
    public ?string $website = null;
}

