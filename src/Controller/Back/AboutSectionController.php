<?php

namespace App\Controller\Back;

use App\Entity\AboutSection;
use App\Form\AboutSectionType;
use App\Repository\AboutSectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @brief Back-office controller for the AboutSection singleton.
 *
 * @date 2026-03-18
 * @author Stephane H.
 */
#[IsGranted('ROLE_USER')]
class AboutSectionController extends AbstractController
{
    /**
     * @brief AboutSectionController constructor.
     *
     * @param AboutSectionRepository $repository The about section repository.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function __construct(
        private readonly AboutSectionRepository $repository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @brief Edits the AboutSection singleton.
     *
     * @param Request $request The HTTP request.
     * @return Response The response rendering the edit form.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function edit(Request $request): Response
    {
        $aboutSection = $this->repository->findSingleton();
        if ($aboutSection === null) {
            $aboutSection = new AboutSection();
        }

        $form = $this->createForm(AboutSectionType::class, $aboutSection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $aboutSection->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($aboutSection);
            $this->entityManager->flush();

            $this->addFlash('success', 'Section "Qui sommes-nous" mise à jour.');

            return $this->redirectToRoute('app_back_about_section_edit');
        }

        return $this->render('back/about/section/form.html.twig', [
            'form' => $form,
        ]);
    }
}

