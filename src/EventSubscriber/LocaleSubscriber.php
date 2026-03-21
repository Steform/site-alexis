<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Locale subscriber: FR by default, DE for german browsers or when chosen.
 *
 * @author Stephane H.
 * @created 2026-03-12
 *
 * @inputs  RequestEvent
 * @outputs Request locale set to fr or de
 */
class LocaleSubscriber implements EventSubscriberInterface
{
    private const SUPPORTED_LOCALES = ['fr', 'de'];
    private const DEFAULT_LOCALE = 'fr';

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $path = $request->getPathInfo();

        // Ensure session is started
        $session = $request->getSession();

        // 1. URL path has priority when /de or /de/...
        if ($path === '/de' || str_starts_with($path, '/de/')) {
            $locale = 'de';
            $session->set('_locale', $locale);
        } else {
            // 2. Session (user choice)
            $sessionLocale = $session->get('_locale');
            if (\is_string($sessionLocale) && \in_array($sessionLocale, self::SUPPORTED_LOCALES, true)) {
                $locale = $sessionLocale;
            } else {
                // 3. Fallback to browser language
                $preferred = $request->getPreferredLanguage(self::SUPPORTED_LOCALES);
                if ($preferred === 'de') {
                    $locale = 'de';
                } else {
                    $locale = self::DEFAULT_LOCALE;
                }
                $session->set('_locale', $locale);
            }
        }

        $request->setLocale($locale);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}

