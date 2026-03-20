<?php

namespace App\Controller\Back;

use App\Entity\DevisTypePrestation;
use App\Form\DevisTypePrestationType;
use App\Repository\DevisTypePrestationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Back-office CRUD for quote service types (Devis).
 *
 * @author Stephane H.
 * @date 2026-03-19
 */
#[IsGranted('ROLE_USER')]
class DevisTypePrestationController extends AbstractController
{
    public function __construct(
        private readonly DevisTypePrestationRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function index(): Response
    {
        return $this->render('back/devis_type_prestation/index.html.twig', [
            'types' => $this->repository->findAllOrdered(),
        ]);
    }

    public function new(Request $request): Response
    {
        $type = new DevisTypePrestation();
        $form = $this->createForm(DevisTypePrestationType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($type);
            $this->em->flush();
            $this->addFlash('success', 'Type de prestation ajouté.');

            return $this->redirectToRoute(
                str_starts_with($request->getLocale(), 'de') ? 'app_back_content_devis_de' : 'app_back_content_devis_fr'
            );
        }

        return $this->render('back/devis_type_prestation/form.html.twig', [
            'type' => $type,
            'form' => $form,
        ]);
    }

    public function edit(Request $request, DevisTypePrestation $type): Response
    {
        $form = $this->createForm(DevisTypePrestationType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Type de prestation modifié.');

            return $this->redirectToRoute(
                str_starts_with($request->getLocale(), 'de') ? 'app_back_content_devis_de' : 'app_back_content_devis_fr'
            );
        }

        return $this->render('back/devis_type_prestation/form.html.twig', [
            'type' => $type,
            'form' => $form,
        ]);
    }

    public function delete(Request $request, DevisTypePrestation $type): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $type->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute(
                str_starts_with($request->getLocale(), 'de') ? 'app_back_content_devis_de' : 'app_back_content_devis_fr'
            );
        }

        $this->em->remove($type);
        $this->em->flush();
        $this->addFlash('success', 'Type de prestation supprimé.');

        return $this->redirectToRoute(
            str_starts_with($request->getLocale(), 'de') ? 'app_back_content_devis_de' : 'app_back_content_devis_fr'
        );
    }
}
