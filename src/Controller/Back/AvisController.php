<?php

namespace App\Controller\Back;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Repository\AvisRepository;
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
 * Back-office CRUD for customer reviews.
 *
 * @author Stephane H.
 * @created 2026-03-11
 */
#[IsGranted('ROLE_USER')]
class AvisController extends AbstractController
{
    public function __construct(
        private readonly AvisRepository $repository,
        private readonly EntityManagerInterface $em,
        private readonly AdminAuditLogger $adminAuditLogger,
        private readonly EntitySnapshotRecorder $entitySnapshotRecorder,
    ) {
    }

    public function index(): Response
    {
        return $this->render('back/avis/index.html.twig', [
            'avis' => $this->repository->findAllOrderedByDate(),
        ]);
    }

    public function new(Request $request): Response
    {
        $avis = new Avis();
        $avis->setDate(new \DateTimeImmutable());

        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($avis);
            $this->em->flush();

            $this->entitySnapshotRecorder->recordAfterCreate($this->em, $avis, EntitySnapshotDomain::AVIS, $this->getUser());
            $this->em->flush();

            $this->adminAuditLogger->log(AdminAuditActions::AVIS_CREATE, [
                'id' => $avis->getId(),
                'auteur' => (string) ($avis->getAuteur() ?? ''),
                'note' => $avis->getNote(),
            ], $this->getUser());

            $this->addFlash('success', 'Avis ajouté.');

            return $this->redirectToRoute('app_back_avis_index');
        }

        return $this->render('back/avis/form.html.twig', [
            'avis' => $avis,
            'form' => $form,
        ]);
    }

    public function edit(Request $request, Avis $avis): Response
    {
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->adminAuditLogger->log(AdminAuditActions::AVIS_UPDATE, [
                'id' => $avis->getId(),
                'auteur' => (string) ($avis->getAuteur() ?? ''),
                'note' => $avis->getNote(),
            ], $this->getUser());

            $this->addFlash('success', 'Avis modifié.');

            return $this->redirectToRoute('app_back_avis_index');
        }

        return $this->render('back/avis/form.html.twig', [
            'avis' => $avis,
            'form' => $form,
        ]);
    }

    public function delete(Request $request, Avis $avis): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $avis->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute('app_back_avis_index');
        }

        $this->entitySnapshotRecorder->recordBeforeDelete($this->em, $avis, EntitySnapshotDomain::AVIS, $this->getUser());

        $this->adminAuditLogger->log(AdminAuditActions::AVIS_DELETE, [
            'id' => $avis->getId(),
            'auteur' => (string) ($avis->getAuteur() ?? ''),
        ], $this->getUser());

        $this->em->remove($avis);
        $this->em->flush();
        $this->addFlash('success', 'Avis supprimé.');

        return $this->redirectToRoute('app_back_avis_index');
    }
}
