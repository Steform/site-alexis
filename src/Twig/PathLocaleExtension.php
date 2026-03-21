<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\RouteLocaleMapper;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension for locale-aware path generation (SEO-friendly URLs).
 *
 * @author Stephane H.
 * @date 2026-03-21
 */
class PathLocaleExtension extends AbstractExtension
{
    public function __construct(
        private readonly RouteLocaleMapper $routeLocaleMapper,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly RequestStack $requestStack
    ) {
    }

    /**
     * @brief Returns Twig functions.
     *
     * @return list<TwigFunction>
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('path_locale', [$this, 'pathLocale'], ['is_safe' => ['html']]),
            new TwigFunction('path_for_locale', [$this, 'pathForLocale'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @brief Generates path for the current request locale.
     *
     * @param string $route FR route name (e.g. app_home, app_services)
     * @param array<string, mixed> $params Route parameters
     * @param int $referenceType UrlGeneratorInterface constant
     * @return string Generated URL
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function pathLocale(string $route, array $params = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $locale = $request?->getLocale();
        $targetRoute = ($locale === 'de')
            ? $this->routeLocaleMapper->getDeRouteName($route)
            : null;
        $routeToUse = $targetRoute ?? $route;
        return $this->urlGenerator->generate($routeToUse, $params, $referenceType);
    }

    /**
     * @brief Generates path for a specific locale (for hreflang, language switcher links).
     *
     * @param string $route FR route name
     * @param string $locale Target locale (fr or de)
     * @param array<string, mixed> $params Route parameters
     * @param int $referenceType UrlGeneratorInterface constant
     * @return string Generated URL
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function pathForLocale(string $route, string $locale, array $params = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_URL): string
    {
        $targetRoute = $this->routeLocaleMapper->getRouteForLocale($route, $locale) ?? $route;
        return $this->urlGenerator->generate($targetRoute, $params, $referenceType);
    }
}
