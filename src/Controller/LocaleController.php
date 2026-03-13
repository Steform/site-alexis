<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Locale switch controller.
 *
 * @author Stephane H.
 * @created 2026-03-12
 *
 * @inputs  Request, locale string
 * @outputs RedirectResponse with updated locale in session
 */
class LocaleController extends AbstractController
{
    /**
     * Switches the current locale and redirects back to the referrer.
     *
     * @param Request $request
     * @param string  $locale
     * @return RedirectResponse
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $allowed = ['fr', 'de'];
        if (!in_array($locale, $allowed, true)) {
            $locale = 'fr';
        }

        $session = $request->getSession();
        $session->set('_locale', $locale);

        $referer = $request->headers->get('referer');
        if (!$referer) {
            $referer = $this->generateUrl('app_home');
        }

        return new RedirectResponse($referer);
    }
}

