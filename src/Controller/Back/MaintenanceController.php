<?php

declare(strict_types=1);

namespace App\Controller\Back;

use App\Service\MaintenanceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Back-office maintenance mode controller.
 *
 * @author Stephane H.
 * @date 2026-03-21
 */
#[IsGranted('ROLE_USER')]
class MaintenanceController extends AbstractController
{
    private const BYPASS_SESSION_KEY = 'maintenance_bypass';

    public function __construct(
        private readonly MaintenanceService $maintenanceService,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator
    ) {
    }

    /**
     * @brief Toggles maintenance mode (admin or owner only).
     *
     * @param Request $request The request
     * @return Response Redirect to dashboard
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function toggle(Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROPRIETAIRE')) {
            throw new AccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('maintenance_toggle', (string) $request->request->get('_token'))) {
            throw new AccessDeniedException('Invalid CSRF token.');
        }

        if ($this->maintenanceService->isActive()) {
            $this->maintenanceService->disable();
            $this->addFlash('success', $this->translator->trans('back.maintenance.disabled', [], 'back'));
        } else {
            $this->maintenanceService->enable();
            $this->addFlash('success', $this->translator->trans('back.maintenance.enabled', [], 'back'));
        }

        return $this->redirectToRoute('app_back_dashboard');
    }

    /**
     * @brief Sets bypass in session and redirects to home (allows working on content during maintenance).
     *
     * @return Response Redirect to home
     * @author Stephane H.
     * @date 2026-03-21
     */
    public function bypass(): Response
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request?->hasSession()) {
            $request->getSession()->set(self::BYPASS_SESSION_KEY, true);
        }

        return $this->redirectToRoute('app_home');
    }
}
