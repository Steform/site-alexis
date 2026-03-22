<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\BackupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @brief Initial site setup when no users exist in the database.
 *
 * @date 2026-03-21
 * @author Stephane H.
 */
class SetupController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly BackupService $backupService,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @brief Displays the setup page with two options: create admin or restore backup.
     *
     * @return Response
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function index(): Response
    {
        if ($this->userRepository->count([]) > 0) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('setup/index.html.twig', [
            'backups' => $this->backupService->listBackups(),
        ]);
    }

    /**
     * @brief Creates the first admin user during initial site setup.
     *
     * @param Request $request
     * @return Response
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function createAdmin(Request $request): Response
    {
        if ($this->userRepository->count([]) > 0) {
            return $this->redirectToRoute('app_login');
        }

        if (!$this->isCsrfTokenValid('setup_create_admin', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_setup');
        }

        $nom = trim($request->request->get('nom', ''));
        $email = trim($request->request->get('email', ''));
        $password = $request->request->get('password', '');
        $passwordConfirm = $request->request->get('password_confirm', '');

        if ($email === '' || $password === '') {
            $this->addFlash('error', 'setup.error.required_fields');
            return $this->redirectToRoute('app_setup');
        }

        if ($password !== $passwordConfirm) {
            $this->addFlash('error', 'setup.error.password_mismatch');
            return $this->redirectToRoute('app_setup');
        }

        if (strlen($password) < 6) {
            $this->addFlash('error', 'setup.error.password_too_short');
            return $this->redirectToRoute('app_setup');
        }

        $user = new User();
        $user->setNom($nom !== '' ? $nom : null);
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        $this->addFlash('success', 'setup.flash.admin_created');

        return $this->redirectToRoute('app_login');
    }

    /**
     * @brief Restores the site from a backup during initial setup.
     *
     * @param Request $request
     * @return Response
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function restoreBackup(Request $request): Response
    {
        if ($this->userRepository->count([]) > 0) {
            return $this->redirectToRoute('app_login');
        }

        if (!$this->isCsrfTokenValid('setup_restore', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_setup');
        }

        $filename = $request->request->get('filename', '');

        if ($filename === '') {
            $this->addFlash('error', 'setup.error.no_backup_selected');
            return $this->redirectToRoute('app_setup');
        }

        try {
            $this->backupService->restoreBackup($filename);
            $this->addFlash('success', 'setup.flash.restored');
        } catch (\Throwable $e) {
            $this->logger->error('Setup restore from existing backup failed.', [
                'filename' => $filename,
                'exception' => $e,
            ]);
            $this->addFlash(
                'error',
                new TranslatableMessage('setup.flash.restore_error_detail', ['%details%' => $e->getMessage()])
            );

            return $this->redirectToRoute('app_setup');
        }

        return $this->redirectToRoute('app_login');
    }

    /**
     * @brief Uploads a ZIP backup file and restores the site from it during initial setup.
     *
     * @param Request $request
     * @return Response
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function uploadBackup(Request $request): Response
    {
        if ($this->userRepository->count([]) > 0) {
            return $this->redirectToRoute('app_login');
        }

        if (!$this->isCsrfTokenValid('setup_upload', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_setup');
        }

        $file = $request->files->get('backup_file');

        if ($file === null) {
            $this->addFlash('error', 'setup.error.no_file');
            return $this->redirectToRoute('app_setup');
        }

        if ($file->getClientOriginalExtension() !== 'zip' && $file->getMimeType() !== 'application/zip') {
            $this->addFlash('error', 'setup.error.invalid_file');
            return $this->redirectToRoute('app_setup');
        }

        try {
            $this->backupService->importAndRestore($file);
            $this->addFlash('success', 'setup.flash.upload_restored');
        } catch (\Throwable $e) {
            $this->logger->error('Setup restore from uploaded backup failed.', [
                'exception' => $e,
            ]);
            $this->addFlash(
                'error',
                new TranslatableMessage('setup.flash.restore_error_detail', ['%details%' => $e->getMessage()])
            );

            return $this->redirectToRoute('app_setup');
        }

        return $this->redirectToRoute('app_login');
    }
}
