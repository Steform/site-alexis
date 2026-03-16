<?php

namespace App\Controller\Back;

use App\Entity\Coordinates;
use App\Form\CoordinatesType;
use App\Repository\CoordinatesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @brief Back-office controller to manage business coordinates.
 *
 * @date 2026-03-16
 * @author Stephane H.
 */
#[IsGranted('ROLE_USER')]
class CoordinatesController extends AbstractController
{
    /**
     * @brief CoordinatesController constructor.
     *
     * @param CoordinatesRepository $coordinatesRepository The coordinates repository.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function __construct(
        private readonly CoordinatesRepository $coordinatesRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @brief Displays and handles the coordinates form.
     *
     * @param Request $request The HTTP request.
     * @return Response The HTTP response for the coordinates form page.
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function index(Request $request): Response
    {
        $coordinates = $this->coordinatesRepository->findSingle();

        if ($coordinates === null) {
            $coordinates = new Coordinates();
        }

        $form = $this->createForm(CoordinatesType::class, $coordinates);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($coordinates);
            $this->entityManager->flush();

            $this->addFlash('success', 'Coordonnées mises à jour.');

            return $this->redirectToRoute('app_back_coordinates');
        }

        return $this->render('back/coordinates/form.html.twig', [
            'form' => $form,
        ]);
    }
}

