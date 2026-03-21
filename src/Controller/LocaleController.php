<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use App\Service\RouteLocaleMapper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Locale switch controller.
 * Redirects to the equivalent URL in the target locale for SEO.
 *
 * @author Stephane H.
 * @created 2026-03-12
 *
 * @inputs  Request, locale string
 * @outputs RedirectResponse with updated locale in session
 */
class LocaleController extends AbstractController
{
    public function __construct(
        private readonly RouteLocaleMapper $routeLocaleMapper,
        private readonly ServiceRepository $serviceRepository
    ) {
    }

    /**
     * Switches the current locale and redirects to the equivalent URL in the target language.
     *
     * @param Request $request
     * @param string  $locale
     * @return RedirectResponse
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $allowed = ['fr', 'de'];
        if (!\in_array($locale, $allowed, true)) {
            $locale = 'fr';
        }

        $session = $request->getSession();
        $session->set('_locale', $locale);

        $path = $request->query->get('from');
        if (!$path && $referer = $request->headers->get('referer')) {
            $path = parse_url($referer, PHP_URL_PATH);
            if (\is_string($path)) {
                $base = $request->getBasePath();
                if ($base !== '' && str_starts_with($path, $base)) {
                    $path = substr($path, \strlen($base)) ?: '/';
                }
            }
        }
        if (\is_string($path)) {
            $current = $this->routeLocaleMapper->getRouteAndParamsFromPath($path);
            if ($current) {
                $targetRoute = $this->routeLocaleMapper->getRouteForLocale($current['route'], $locale);
                if ($targetRoute) {
                    $params = $current['params'];
                    if (isset($params['slug']) && \in_array($current['route'], ['app_services_show', 'app_services_show_de'], true)) {
                        $service = $this->serviceRepository->findBySlug($params['slug']);
                        if ($service) {
                            $params['slug'] = $service->getSlugForLocale($locale);
                        }
                    }

                    return $this->redirectToRoute($targetRoute, $params);
                }
            }
        }

        if ($locale === 'de') {
            return $this->redirectToRoute('app_home_de');
        }
        return $this->redirectToRoute('app_home');
    }
}

