<?php

namespace App\Controller\Back;

use App\Entity\Horaires;
use App\Form\HorairesType;
use App\Repository\HorairesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Back-office CRUD for opening hours.
 *
 * @author Stephane H.
 * @created 2026-03-11
 */
#[IsGranted('ROLE_USER')]
class HorairesController extends AbstractController
{
    private const DAYS = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];

    public function __construct(
        private readonly HorairesRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function index(): Response
    {
        $horaires = $this->repository->findAllOrdered();

        if (empty($horaires)) {
            foreach (self::DAYS as $jour) {
                $h = new Horaires();
                $h->setJour($jour);
                $this->em->persist($h);
            }
            $this->em->flush();
            $horaires = $this->repository->findAllOrdered();
        }

        return $this->render('back/horaires/index.html.twig', [
            'horaires' => $horaires,
        ]);
    }

    public function edit(Request $request, Horaires $horaires): Response
    {
        $form = $this->createForm(HorairesType::class, $horaires, ['edit_mode' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Horaires mis à jour.');

            return $this->redirectToRoute('app_back_horaires_index');
        }

        return $this->render('back/horaires/edit.html.twig', [
            'horaires' => $horaires,
            'form' => $form,
        ]);
    }
}
