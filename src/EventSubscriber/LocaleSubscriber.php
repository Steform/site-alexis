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

        // Ensure session is started
        $session = $request->getSession();

        // 1. User choice (selector) has priority
        $sessionLocale = $session->get('_locale');
        if (\is_string($sessionLocale) && \in_array($sessionLocale, self::SUPPORTED_LOCALES, true)) {
            $locale = $sessionLocale;
        } else {
            // 2. Fallback to browser language
            $preferred = $request->getPreferredLanguage(self::SUPPORTED_LOCALES);
            if ($preferred === 'de') {
                $locale = 'de';
            } else {
                $locale = self::DEFAULT_LOCALE;
            }
            $session->set('_locale', $locale);
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

