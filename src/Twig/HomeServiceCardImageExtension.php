<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Service;
use App\Service\HomeServiceCardImagePathResolver;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @brief Twig helpers for home service card images, list teaser images, and card titles on public pages.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
class HomeServiceCardImageExtension extends AbstractExtension
{
    public function __construct(
        private readonly HomeServiceCardImagePathResolver $homeServiceCardImagePathResolver,
    ) {
    }

    /**
     * @brief Returns Twig functions.
     *
     * @return list<TwigFunction>
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('service_home_card_image', [$this, 'serviceHomeCardImage']),
            new TwigFunction('service_list_teaser_image', [$this, 'serviceListTeaserImage']),
            new TwigFunction('service_home_card_title', [$this, 'serviceHomeCardTitle']),
        ];
    }

    /**
     * @brief Resolves the image path for a service (home CMS card or entity fallback).
     *
     * @param Service $service The service.
     * @param array<string, mixed> $homePageContent Home page content array.
     * @return string Relative path for asset().
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function serviceHomeCardImage(Service $service, array $homePageContent): string
    {
        $flat = [];
        foreach ($homePageContent as $k => $v) {
            $flat[(string) $k] = \is_scalar($v) ? (string) $v : '';
        }

        return $this->homeServiceCardImagePathResolver->getImagePathForService($service, $flat);
    }

    /**
     * @brief Resolves the services list page thumbnail path (entity teaser image, or home card CMS path as fallback).
     *
     * @param Service $service The service.
     * @param array<string, mixed> $homePageContent Home page content array.
     * @return string Relative path for asset().
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function serviceListTeaserImage(Service $service, array $homePageContent): string
    {
        $flat = [];
        foreach ($homePageContent as $k => $v) {
            $flat[(string) $k] = \is_scalar($v) ? (string) $v : '';
        }

        return $this->homeServiceCardImagePathResolver->getListTeaserImagePath($service, $flat);
    }

    /**
     * @brief Resolves the card title for a service (home CMS slot, translation fallback, or empty for entity title).
     *
     * @param Service $service The service.
     * @param array<string, mixed> $homePageContent Home page content array.
     * @param string $contentLocale Content locale (`fr` or `de`).
     * @return string Title string; may be empty when the service is not mapped to a home card slot.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function serviceHomeCardTitle(Service $service, array $homePageContent, string $contentLocale): string
    {
        $flat = [];
        foreach ($homePageContent as $k => $v) {
            $flat[(string) $k] = \is_scalar($v) ? (string) $v : '';
        }

        return $this->homeServiceCardImagePathResolver->getCardTitleForService($service, $flat, $contentLocale);
    }
}
