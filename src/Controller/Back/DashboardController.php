<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Back-office dashboard.
 *
 * @author Stephane H.
 * @created 2026-03-11
 */
#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    public function index(RequestStack $requestStack): Response
    {
        // #region agent log
        try {
            $peek = $requestStack->getSession()->getFlashBag()->peekAll();
            $types = [];
            foreach ($peek as $label => $messages) {
                foreach ($messages as $m) {
                    $types[] = ['label' => $label, 'type' => \get_debug_type($m)];
                }
            }
            $line = json_encode([
                'sessionId' => 'ace7b8',
                'hypothesisId' => 'H1',
                'location' => 'DashboardController::index',
                'message' => 'flash peek before render',
                'data' => ['flash_types' => $types],
                'timestamp' => (int) (microtime(true) * 1000),
            ], \JSON_UNESCAPED_UNICODE) . "\n";
            $logPath = \dirname(__DIR__, 3) . '/debug-ace7b8.log';
            file_put_contents($logPath, $line, \FILE_APPEND | \LOCK_EX);
        } catch (\Throwable) {
        }
        // #endregion

        return $this->render('back/dashboard.html.twig');
    }
}
