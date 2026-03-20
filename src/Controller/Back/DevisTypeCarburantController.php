<?php

namespace App\Controller\Back;

use App\Entity\DevisTypeCarburant;
use App\Form\DevisTypeCarburantType;
use App\Repository\DevisTypeCarburantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Back-office CRUD for quote fuel types (Devis).
 *
 * @author Stephane H.
 * @date 2026-03-19
 */
#[IsGranted('ROLE_USER')]
class DevisTypeCarburantController extends AbstractController
{
    public function __construct(
        private readonly DevisTypeCarburantRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @brief Lists all fuel types ordered by ordre.
     *
     * @return Response
     * @author Stephane H.
     * @date 2026-03-19
     */
    public function index(): Response
    {
        return $this->render('back/devis_type_carburant/index.html.twig', [
            'types' => $this->repository->findAllOrdered(),
        ]);
    }

    /**
     * @brief Creates a new fuel type.
     *
     * @param Request $request
     * @return Response
     * @author Stephane H.
     * @date 2026-03-19
     */
    public function new(Request $request): Response
    {
        $type = new DevisTypeCarburant();
        $form = $this->createForm(DevisTypeCarburantType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($type);
            $this->em->flush();
            $this->addFlash('success', 'Type de carburant ajouté.');

            return $this->redirectToRoute(
                str_starts_with($request->getLocale(), 'de') ? 'app_back_content_devis_de' : 'app_back_content_devis_fr'
            );
        }

        return $this->render('back/devis_type_carburant/form.html.twig', [
            'type' => $type,
            'form' => $form,
        ]);
    }

    /**
     * @brief Edits an existing fuel type.
     *
     * @param Request $request
     * @param DevisTypeCarburant $type
     * @return Response
     * @author Stephane H.
     * @date 2026-03-19
     */
    public function edit(Request $request, DevisTypeCarburant $type): Response
    {
        $form = $this->createForm(DevisTypeCarburantType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Type de carburant modifié.');

            return $this->redirectToRoute(
                str_starts_with($request->getLocale(), 'de') ? 'app_back_content_devis_de' : 'app_back_content_devis_fr'
            );
        }

        return $this->render('back/devis_type_carburant/form.html.twig', [
            'type' => $type,
            'form' => $form,
        ]);
    }

    /**
     * @brief Deletes a fuel type.
     *
     * @param Request $request
     * @param DevisTypeCarburant $type
     * @return Response
     * @author Stephane H.
     * @date 2026-03-19
     */
    public function delete(Request $request, DevisTypeCarburant $type): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $type->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute('app_back_devis_type_carburant_index');
        }

        $this->em->remove($type);
        $this->em->flush();
        $this->addFlash('success', 'Type de carburant supprimé.');

        return $this->redirectToRoute(
            str_starts_with($request->getLocale(), 'de') ? 'app_back_content_devis_de' : 'app_back_content_devis_fr'
        );
    }
}
