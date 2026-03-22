<?php

namespace App\Controller\Back;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Service\AdminAuditActions;
use App\Service\AdminAuditLogger;
use App\Service\EntitySnapshotDomain;
use App\Service\EntitySnapshotRecorder;
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
        private readonly AdminAuditLogger $adminAuditLogger,
        private readonly EntitySnapshotRecorder $entitySnapshotRecorder,
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

            $this->adminAuditLogger->log(AdminAuditActions::MESSAGE_CREATE, [
                'id' => $message->getId(),
                'contenuPreview' => $this->shortPreview($message->getContenu()),
                'dateDebut' => $message->getDateDebut()?->format(\DateTimeInterface::ATOM),
                'dateFin' => $message->getDateFin()?->format(\DateTimeInterface::ATOM),
            ], $this->getUser());

            $this->addFlash('success', 'Message ajouté.');

            return $this->redirectToRoute('app_back_message_index');
        }

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
            $this->entitySnapshotRecorder->recordBeforeUpdate($this->em, $message, EntitySnapshotDomain::MESSAGE, $this->getUser());
            $this->em->flush();

            $this->adminAuditLogger->log(AdminAuditActions::MESSAGE_UPDATE, [
                'id' => $message->getId(),
                'contenuPreview' => $this->shortPreview($message->getContenu()),
                'dateDebut' => $message->getDateDebut()?->format(\DateTimeInterface::ATOM),
                'dateFin' => $message->getDateFin()?->format(\DateTimeInterface::ATOM),
            ], $this->getUser());

            $this->addFlash('success', 'Message modifié.');

            return $this->redirectToRoute('app_back_message_index');
        }

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

        $this->entitySnapshotRecorder->recordBeforeDelete($this->em, $message, EntitySnapshotDomain::MESSAGE, $this->getUser());

        $this->adminAuditLogger->log(AdminAuditActions::MESSAGE_DELETE, [
            'id' => $message->getId(),
            'contenuPreview' => $this->shortPreview($message->getContenu()),
        ], $this->getUser());

        $this->em->remove($message);
        $this->em->flush();
        $this->addFlash('success', 'Message supprimé.');

        return $this->redirectToRoute('app_back_message_index');
    }

    /**
     * @brief Truncates message text for audit payloads (no full body stored).
     *
     * @param string|null $text The raw text.
     * @return string Short preview.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function shortPreview(?string $text): string
    {
        $t = trim((string) $text);
        if ($t === '') {
            return '';
        }
        if (strlen($t) <= 120) {
            return $t;
        }

        return substr($t, 0, 120) . '…';
    }
}
