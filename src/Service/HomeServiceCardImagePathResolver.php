<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Service;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @brief Resolves public image path and card title for a service using home CMS card slots (same mapping as index.html.twig).
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
class HomeServiceCardImagePathResolver
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * French slug to home card slot (1–5), aligned with templates/public/index.html.twig.
     */
    private const SLUG_TO_SLOT = [
        'reparation-carrosserie-peinture' => 1,
        'debosselage' => 2,
        'pare-brise-optique' => 3,
        'entretien-mecanique' => 5,
        'vehicule-pret-courtoisie' => 4,
    ];

    /**
     * Default public-relative image per slot when CMS value is empty (same defaults as index.html.twig).
     */
    private const SLOT_DEFAULT_IMAGE = [
        1 => 'images/services/reparation-carrosserie-peinture.webp',
        2 => 'images/services/debosselage.webp',
        3 => 'images/services/pare-brise.webp',
        4 => 'images/services/vehicule-pret-courtoisie.webp',
        5 => 'images/services/mecanique.webp',
    ];

    /**
     * @brief Returns the image path for home-style cards (CMS slots 1–5, defaults, or entity image when unmapped).
     *
     * Uses services.cardN.image from home page content when the service FR slug matches a home card; otherwise service.image.
     *
     * @param Service $service The service entity.
     * @param array<string, string> $homePageContent Flat home page content for the current locale (from ContentBlockManager::getPageContent('home', ...)).
     * @return string Relative path suitable for asset().
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getImagePathForService(Service $service, array $homePageContent): string
    {
        $slug = (string) ($service->getSlug() ?? '');
        if ($slug === '' || !isset(self::SLUG_TO_SLOT[$slug])) {
            return (string) ($service->getImage() ?? '');
        }

        $slot = self::SLUG_TO_SLOT[$slug];
        $key = 'services.card' . $slot . '.image';
        $fromCms = isset($homePageContent[$key]) ? trim((string) $homePageContent[$key]) : '';
        if ($fromCms !== '') {
            return $fromCms;
        }

        return self::SLOT_DEFAULT_IMAGE[$slot] ?? (string) ($service->getImage() ?? '');
    }

    /**
     * @brief Returns the public "Nos services" list thumbnail path: entity list image (level 2) or home card path (level 1) as fallback.
     *
     * @param Service $service The service entity.
     * @param array<string, string> $homePageContent Flat home page content for the current locale.
     * @return string Relative path suitable for asset().
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getListTeaserImagePath(Service $service, array $homePageContent): string
    {
        $fromEntity = trim((string) ($service->getImage() ?? ''));
        if ($fromEntity !== '') {
            return $fromEntity;
        }

        return $this->getImagePathForService($service, $homePageContent);
    }

    /**
     * @brief Returns the visible card title from home CMS or translation fallback when the service maps to a home card slot.
     *
     * When the French slug has no mapped slot, returns an empty string so callers can use the entity title.
     *
     * @param Service $service The service entity.
     * @param array<string, string> $homePageContent Flat home page content for the current locale.
     * @param string $contentLocale Content locale (`fr` or `de`).
     * @return string Non-empty title for mapped services; empty string when not mapped to a home card.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getCardTitleForService(Service $service, array $homePageContent, string $contentLocale): string
    {
        $slug = (string) ($service->getSlug() ?? '');
        if ($slug === '' || !isset(self::SLUG_TO_SLOT[$slug])) {
            return '';
        }

        $slot = self::SLUG_TO_SLOT[$slug];
        $key = 'services.card' . $slot . '.title';
        $fromCms = isset($homePageContent[$key]) ? trim((string) $homePageContent[$key]) : '';
        if ($fromCms !== '') {
            return $fromCms;
        }

        return $this->translator->trans('home.services.card' . $slot . '.title', [], 'messages', $contentLocale);
    }
}
