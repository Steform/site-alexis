<?php

namespace App\Controller\Public;

use App\Dto\DevisRequest;
use App\Form\DevisType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
/**
 * Public quote request controller.
 *
 * @author Stephane H.
 * @created 2026-03-11
 */
class DevisController extends AbstractController
{
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
        $form = $this->createForm(DevisType::class, $devis);
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
                ->html($this->renderView('public/devis/email.html.twig', ['devis' => $data]));

            $mailer->send($email);

            $this->addFlash('success', $this->trans('devis.flash.success'));

            return $this->redirectToRoute('app_devis');
        }

        return $this->render('public/devis/index.html.twig', [
            'form' => $form,
        ]);
    }
}
