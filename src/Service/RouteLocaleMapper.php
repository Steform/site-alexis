<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Maps public routes between FR and DE locales for SEO-friendly URLs.
 *
 * @author Stephane H.
 * @date 2026-03-21
 */
class RouteLocaleMapper
{
    private const FR_TO_DE = [
        'app_home' => 'app_home_de',
        'app_contact' => 'app_contact_de',
        'app_devis' => 'app_devis_de',
        'app_gallery' => 'app_gallery_de',
        'app_mentions_legales' => 'app_mentions_legales_de',
        'app_qui_sommes_nous' => 'app_qui_sommes_nous_de',
        'app_horaires_public' => 'app_horaires_public_de',
        'app_services' => 'app_services_de',
        'app_services_show' => 'app_services_show_de',
        'app_maintenance' => 'app_maintenance_de',
    ];

    private const DE_TO_FR = [
        'app_home_de' => 'app_home',
        'app_contact_de' => 'app_contact',
        'app_devis_de' => 'app_devis',
        'app_gallery_de' => 'app_gallery',
        'app_mentions_legales_de' => 'app_mentions_legales',
        'app_qui_sommes_nous_de' => 'app_qui_sommes_nous',
        'app_horaires_public_de' => 'app_horaires_public',
        'app_services_de' => 'app_services',
        'app_services_show_de' => 'app_services_show',
        'app_maintenance_de' => 'app_maintenance',
    ];

    /**
     * @brief Returns the route name for the target locale.
     *
     * @param string $currentRoute The current route name
     * @param string $targetLocale Target locale (fr or de)
     * @return string|null The route name for target locale, or null if no mapping
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function getRouteForLocale(string $currentRoute, string $targetLocale): ?string
    {
        if ($targetLocale === 'de') {
            return self::FR_TO_DE[$currentRoute] ?? null;
        }
        if ($targetLocale === 'fr') {
            return self::DE_TO_FR[$currentRoute] ?? null;
        }
        return null;
    }

    /**
     * @brief Returns the FR route name (normalizes _de suffix).
     *
     * @param string $route The route name (FR or DE)
     * @return string The base FR route name for path_locale
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function getFrRouteName(string $route): string
    {
        return self::DE_TO_FR[$route] ?? $route;
    }

    /**
     * @brief Returns the DE route name for a given FR route.
     *
     * @param string $frRoute The FR route name
     * @return string|null The DE route name or null
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function getDeRouteName(string $frRoute): ?string
    {
        return self::FR_TO_DE[$frRoute] ?? null;
    }

    /**
     * Path to route mapping (FR and DE paths).
     */
    private const PATH_TO_ROUTE = [
        '/' => 'app_home',
        '/de' => 'app_home_de',
        '/contact' => 'app_contact',
        '/de/kontakt' => 'app_contact_de',
        '/devis' => 'app_devis',
        '/de/angebot' => 'app_devis_de',
        '/galerie' => 'app_gallery',
        '/de/galerie' => 'app_gallery_de',
        '/mentions-legales' => 'app_mentions_legales',
        '/de/impressum' => 'app_mentions_legales_de',
        '/qui-sommes-nous' => 'app_qui_sommes_nous',
        '/de/uber-uns' => 'app_qui_sommes_nous_de',
        '/horaires' => 'app_horaires_public',
        '/de/offnungszeiten' => 'app_horaires_public_de',
        '/services' => 'app_services',
        '/de/leistungen' => 'app_services_de',
        '/maintenance' => 'app_maintenance',
        '/de/wartung' => 'app_maintenance_de',
    ];

    /**
     * @brief Returns route name and params from a path (for locale switch redirect).
     *
     * @param string $path Request path (e.g. from referer)
     * @return array{route: string, params: array<string, mixed>}|null
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function getRouteAndParamsFromPath(string $path): ?array
    {
        $path = '/' . trim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        // Exact match
        if (isset(self::PATH_TO_ROUTE[$path])) {
            return ['route' => self::PATH_TO_ROUTE[$path], 'params' => []];
        }

        // /services/{slug} or /de/leistungen/{slug}
        if (preg_match('#^/services/([a-z0-9\-]+)$#', $path, $m)) {
            return ['route' => 'app_services_show', 'params' => ['slug' => $m[1]]];
        }
        if (preg_match('#^/de/leistungen/([a-z0-9\-]+)$#', $path, $m)) {
            return ['route' => 'app_services_show_de', 'params' => ['slug' => $m[1]]];
        }

        return null;
    }
}
