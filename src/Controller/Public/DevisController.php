<?php

namespace App\Controller\Public;

use App\Dto\DevisRequest;
use App\Form\DevisType;
use App\Repository\DevisTypeCarburantRepository;
use App\Repository\DevisTypePrestationRepository;
use App\Service\ContentBlockManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Public quote request controller.
 *
 * @author Stephane H.
 * @created 2026-03-11
 */
class DevisController extends AbstractController
{
    public function __construct(
        private readonly DevisTypePrestationRepository $devisTypeRepository,
        private readonly DevisTypeCarburantRepository $devisTypeCarburantRepository,
        private readonly ContentBlockManager $contentBlockManager,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * Displays the quote form and handles submission.
     *
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     */
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $devis = new DevisRequest();
        $locale = str_starts_with($request->getLocale(), 'de') ? 'de' : 'fr';
        $form = $this->createForm(DevisType::class, $devis, [
            'type_prestation_choices' => $this->devisTypeRepository->findActiveOrdered(),
            'type_carburant_choices' => $this->devisTypeCarburantRepository->findActiveOrdered(),
            'locale' => $locale,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data->website !== null && $data->website !== '') {
                return $this->redirectToRoute('app_devis');
            }

            $recipient = $this->getParameter('devis.email_receiver');
            $from = $this->getParameter('mailer.from');

            $email = (new Email())
                ->from($from)
                ->to($recipient)
                ->replyTo($data->email)
                ->subject('[Carrosserie Lino] Nouvelle demande de devis')
                ->html($this->renderView('public/devis/email.html.twig', [
                    'devis' => $data,
                    'typeLabel' => $this->getTypeLabel($data->typePrestation, $locale),
                    'carburantLabel' => $this->getCarburantLabel($data->typeCarburant, $locale),
                ]));

            foreach ($data->photos ?? [] as $file) {
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $email->attachFromPath($file->getPathname(), $file->getClientOriginalName(), $file->getMimeType());
                }
            }

            $mailer->send($email);

            $this->addFlash('success', $this->translator->trans('devis.flash.success'));

            return $this->redirectToRoute('app_devis');
        }

        $contentLocale = str_starts_with($request->getLocale(), 'de') ? 'de' : 'fr';
        $devisContent = $this->contentBlockManager->getPageContent('devis', $contentLocale);
        $devisColors = $this->contentBlockManager->getPageColors('devis', $contentLocale);

        return $this->render('public/devis/index.html.twig', [
            'form' => $form,
            'devis_content' => $devisContent,
            'devis_colors' => $devisColors,
        ]);
    }

    private function getTypeLabel(?string $code, string $locale): string
    {
        if (!$code) {
            return '—';
        }
        $type = $this->devisTypeRepository->findOneBy(['code' => $code]);
        if (!$type) {
            return $code;
        }
        return $locale === 'de' && $type->getLabelDe() ? $type->getLabelDe() : $type->getLabel();
    }

    /**
     * Resolves fuel type label from code according to locale.
     *
     * @param string|null $code Fuel type code
     * @param string $locale Locale (fr or de)
     * @return string
     * @author Stephane H.
     * @date 2026-03-19
     */
    private function getCarburantLabel(?string $code, string $locale): string
    {
        if (!$code) {
            return '—';
        }
        $type = $this->devisTypeCarburantRepository->findOneBy(['code' => $code]);
        if (!$type) {
            return $code;
        }
        return $locale === 'de' && $type->getLabelDe() ? $type->getLabelDe() : $type->getLabel();
    }
}
