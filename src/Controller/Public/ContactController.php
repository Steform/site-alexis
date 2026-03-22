<?php

namespace App\Controller\Public;

use App\Dto\ContactRequest;
use App\Form\ContactType;
use App\Repository\CoordinatesRepository;
use App\Repository\HorairesRepository;
use App\Service\OpeningHoursFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @brief Displays the public contact page.
 *
 * @date 2026-03-16
 * @author Stephane H.
 */
class ContactController extends AbstractController
{
    /**
     * @brief ContactController constructor.
     *
     * @param CoordinatesRepository $coordinatesRepository The coordinates repository.
     * @param HorairesRepository $horairesRepository The opening hours repository.
     * @param OpeningHoursFormatter $openingHoursFormatter The opening hours formatter.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function __construct(
        private readonly CoordinatesRepository $coordinatesRepository,
        private readonly HorairesRepository $horairesRepository,
        private readonly OpeningHoursFormatter $openingHoursFormatter,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @brief Shows the contact page.
     *
     * @param Request $request The HTTP request.
     * @param MailerInterface $mailer The mailer service.
     * @return Response The HTTP response with the contact template.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $coordinates = $this->coordinatesRepository->findSingle();
        $horaires = $this->horairesRepository->findAllOrdered();
        $locale = $request->getLocale();
        $horairesCompact = $this->openingHoursFormatter->formatCompact($horaires, $locale);
        $horairesFull = $this->openingHoursFormatter->formatFull($horaires, $locale);

        $contactRequest = new ContactRequest();
        $form = $this->createForm(ContactType::class, $contactRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ContactRequest $data */
            $data = $form->getData();

            if ($data->website !== null && $data->website !== '') {
                return $this->redirectToRoute('app_contact');
            }

            $recipient = $this->getParameter('devis.email_receiver');
            $from = $this->getParameter('mailer.from');

            $email = (new Email())
                ->from($from)
                ->to($recipient)
                ->replyTo($data->email)
                ->subject('[Carrosserie Lino] Nouveau message de contact')
                ->html($this->renderView('public/contact_email.html.twig', [
                    'contact' => $data,
                ]));

            $mailer->send($email);

            $this->addFlash('success', $this->translator->trans('contact.flash.success'));

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('public/contact.html.twig', [
            'form' => $form,
            'coordinates' => $coordinates,
            'horaires_compact' => $horairesCompact,
            'horaires_full' => $horairesFull,
        ]);
    }
}

