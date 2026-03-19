<?php

namespace App\Controller\Public;

use App\Service\ContentBlockManager;
use App\Service\HtmlContentSanitizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Qui sommes-nous page - Alexis Haffner identity.
 *
 * @author Stephane H.
 * @created 2026-03-12
 *
 * @inputs  None
 * @outputs Qui sommes-nous page with Alexis identity
 */
class QuiSommesNousController extends AbstractController
{
    /**
     * @brief QuiSommesNousController constructor.
     *
     * @param ContentBlockManager $contentBlockManager The content block manager.
     * @param HtmlContentSanitizer $htmlContentSanitizer The HTML sanitizer.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function __construct(
        private readonly ContentBlockManager $contentBlockManager,
        private readonly HtmlContentSanitizer $htmlContentSanitizer,
    ) {
    }

    /**
     * Renders the "Qui sommes-nous" page with Alexis Haffner identity.
     *
     * @param Request $request The HTTP request.
     * @return Response
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function index(Request $request): Response
    {
        $locale = str_starts_with($request->getLocale(), 'de') ? 'de' : 'fr';
        $content = $this->contentBlockManager->getPageContent('qui_sommes_nous', $locale);
        $colors = $this->contentBlockManager->getPageColors('qui_sommes_nous', $locale);

        $content['alexis.lead'] = $this->htmlContentSanitizer->sanitize($content['alexis.lead'] ?? '');
        $content['alexis.text1'] = $this->htmlContentSanitizer->sanitize($content['alexis.text1'] ?? '');
        $content['alexis.text2'] = $this->htmlContentSanitizer->sanitize($content['alexis.text2'] ?? '');

        return $this->render('public/qui_sommes_nous.html.twig', [
            'qui_content' => $content,
            'qui_colors' => $colors,
        ]);
    }
}
