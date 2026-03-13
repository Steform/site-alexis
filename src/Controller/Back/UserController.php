<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Back-office CRUD for users (admin only).
 *
 * @author Stephane H.
 * @created 2026-03-11
 */
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function index(): Response
    {
        return $this->render('back/user/index.html.twig', [
            'users' => $this->repository->findBy([], ['email' => 'ASC']),
        ]);
    }

    public function new(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Utilisateur créé.');

            return $this->redirectToRoute('app_back_user_index');
        }

        return $this->render('back/user/form.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword !== '' && $plainPassword !== null) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
            }
            $this->em->flush();
            $this->addFlash('success', 'Utilisateur modifié.');

            return $this->redirectToRoute('app_back_user_index');
        }

        return $this->render('back/user/form.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    public function delete(Request $request, User $user): Response
    {
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('app_back_user_index');
        }

        if (!$this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute('app_back_user_index');
        }

        $this->em->remove($user);
        $this->em->flush();
        $this->addFlash('success', 'Utilisateur supprimé.');

        return $this->redirectToRoute('app_back_user_index');
    }
}
