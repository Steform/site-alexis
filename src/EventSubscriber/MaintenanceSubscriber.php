<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Service\MaintenanceService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Maintenance mode subscriber.
 * Redirects public requests to maintenance page when maintenance is active,
 * unless user is logged in and has bypass enabled.
 *
 * @author Stephane H.
 * @date 2026-03-21
 */
class MaintenanceSubscriber implements EventSubscriberInterface
{
    private const BYPASS_SESSION_KEY = 'maintenance_bypass';

    /**
     * Path prefixes that are never redirected (back-office, login, maintenance page, switch-locale, assets).
     */
    private const EXCLUDED_PREFIXES = ['/back', '/login', '/logout', '/maintenance', '/de/wartung', '/switch-locale', '/_', '/build'];

    public function __construct(
        private readonly MaintenanceService $maintenanceService,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

    /**
     * @brief Handles kernel request to redirect to maintenance when needed.
     *
     * @param RequestEvent $event The request event
     * @return void
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $path = $request->getPathInfo();

        foreach (self::EXCLUDED_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return;
            }
        }

        if (!$this->maintenanceService->isActive()) {
            return;
        }

        $session = $request->getSession();
        $hasBypass = $session->get(self::BYPASS_SESSION_KEY, false);
        $isAuthenticated = $this->authorizationChecker->isGranted('ROLE_USER');

        if ($isAuthenticated && $hasBypass) {
            return;
        }

        $route = str_starts_with($path, '/de') ? 'app_maintenance_de' : 'app_maintenance';
        $event->setResponse(new RedirectResponse(
            $this->urlGenerator->generate($route),
            RedirectResponse::HTTP_TEMPORARY_REDIRECT
        ));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 5]],
        ];
    }
}
