<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @brief Redirects all requests to the setup page when no users exist in the database.
 *
 * Runs at a higher priority than MaintenanceSubscriber so that the setup
 * requirement is enforced before any other access-control logic.
 *
 * @author Stephane H.
 * @date 2026-03-22
 */
class SetupSubscriber implements EventSubscriberInterface
{
    private const EXCLUDED_PREFIXES = ['/setup', '/_', '/build'];

    private ?bool $hasUsers = null;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @brief Redirects to the setup page when no users exist, unless the path is excluded.
     *
     * @param RequestEvent $event The kernel request event.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $path = $event->getRequest()->getPathInfo();

        foreach (self::EXCLUDED_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return;
            }
        }

        if ($this->hasUsers()) {
            return;
        }

        $event->setResponse(new RedirectResponse(
            $this->urlGenerator->generate('app_setup'),
            RedirectResponse::HTTP_TEMPORARY_REDIRECT
        ));
    }

    /**
     * @brief Checks whether at least one user exists (cached per request).
     *
     * @return bool
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function hasUsers(): bool
    {
        if ($this->hasUsers === null) {
            $this->hasUsers = $this->userRepository->count([]) > 0;
        }

        return $this->hasUsers;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 10]],
        ];
    }
}
