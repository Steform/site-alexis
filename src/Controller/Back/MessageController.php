<?php

namespace App\Controller\Back;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Back-office CRUD for bounded-date messages.
 *
 * @author Stephane H.
 * @created 2026-03-11
 */
#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    public function __construct(
        private readonly MessageRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function index(): Response
    {
        return $this->render('back/message/index.html.twig', [
            'messages' => $this->repository->findBy([], ['dateDebut' => 'DESC']),
        ]);
    }

    public function new(Request $request): Response
    {
        $message = new Message();
        $now = new \DateTimeImmutable();
        $message->setDateDebut($now);
        $message->setDateFin($now->modify('+7 days'));

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($message);
            $this->em->flush();
            $this->addFlash('success', 'Message ajouté.');

            return $this->redirectToRoute('app_back_message_index');
        }

        // #region agent log
        @file_put_contents(
            (string) $this->getParameter('kernel.project_dir') . '/debug-8a5f96.log',
            json_encode([
                'sessionId' => '8a5f96',
                'runId' => 'pre-fix',
                'hypothesisId' => 'H1',
                'location' => 'MessageController.php:new',
                'message' => 'Form fields created for new message',
                'data' => [
                    'fields' => array_keys(iterator_to_array($form, true)),
                    'hasContenu' => $form->has('contenu'),
                    'hasContenuDe' => $form->has('contenuDe'),
                    'hasDateDebut' => $form->has('dateDebut'),
                    'hasDateFin' => $form->has('dateFin'),
                ],
                'timestamp' => (int) round(microtime(true) * 1000),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND
        );
        // #endregion

        return $this->render('back/message/form.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    public function edit(Request $request, Message $message): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Message modifié.');

            return $this->redirectToRoute('app_back_message_index');
        }

        // #region agent log
        @file_put_contents(
            (string) $this->getParameter('kernel.project_dir') . '/debug-8a5f96.log',
            json_encode([
                'sessionId' => '8a5f96',
                'runId' => 'pre-fix',
                'hypothesisId' => 'H2',
                'location' => 'MessageController.php:edit',
                'message' => 'Form fields created for edit message',
                'data' => [
                    'messageId' => $message->getId(),
                    'fields' => array_keys(iterator_to_array($form, true)),
                    'hasContenuDe' => $form->has('contenuDe'),
                ],
                'timestamp' => (int) round(microtime(true) * 1000),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND
        );
        // #endregion

        return $this->render('back/message/form.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    public function delete(Request $request, Message $message): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute('app_back_message_index');
        }
        $this->em->remove($message);
        $this->em->flush();
        $this->addFlash('success', 'Message supprimé.');

        return $this->redirectToRoute('app_back_message_index');
    }
}
