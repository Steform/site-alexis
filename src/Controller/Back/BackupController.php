<?php

namespace App\Controller\Back;

use App\Service\BackupService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @brief Back-office backup management (admin only).
 *
 * @date 2026-03-21
 * @author Stephane H.
 */
#[IsGranted('ROLE_ADMIN')]
class BackupController extends AbstractController
{
    public function __construct(
        private readonly BackupService $backupService,
    ) {
    }

    /**
     * @brief Displays the list of available backups.
     *
     * @return Response
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function index(): Response
    {
        return $this->render('back/backup/index.html.twig', [
            'backups' => $this->backupService->listBackups(),
        ]);
    }

    /**
     * @brief Creates a new full backup of the site.
     *
     * @param Request $request
     * @return Response
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function create(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('backup_create', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_back_backup_index');
        }

        try {
            $filename = $this->backupService->createBackup();
            $this->addFlash('success', 'back.backup.flash.created');
        } catch (\Throwable $e) {
            $this->addFlash('error', 'back.backup.flash.error');
        }

        return $this->redirectToRoute('app_back_backup_index');
    }

    /**
     * @brief Downloads a backup ZIP file.
     *
     * @param string $filename The backup filename.
     * @return Response
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function download(string $filename): Response
    {
        $path = $this->backupService->getBackupPath($filename);
        if (!file_exists($path)) {
            throw $this->createNotFoundException('Backup not found.');
        }

        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, basename($path));

        return $response;
    }

    /**
     * @brief Restores the site from a backup archive.
     *
     * @param Request $request
     * @param string $filename The backup filename.
     * @return Response
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function restore(Request $request, string $filename): Response
    {
        if (!$this->isCsrfTokenValid('backup_restore_' . $filename, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_back_backup_index');
        }

        try {
            $this->backupService->restoreBackup($filename);
            $this->addFlash('success', 'back.backup.flash.restored');
        } catch (\Throwable $e) {
            $this->addFlash('error', 'back.backup.flash.error');
        }

        return $this->redirectToRoute('app_back_backup_index');
    }

    /**
     * @brief Uploads a backup ZIP and restores the site from it.
     *
     * @param Request $request
     * @return Response
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function upload(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('backup_upload', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_back_backup_index');
        }

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $file */
        $file = $request->files->get('backup_file');

        if ($file === null || !$file->isValid()) {
            $this->addFlash('error', 'back.backup.flash.upload_invalid');
            return $this->redirectToRoute('app_back_backup_index');
        }

        if ($file->getClientOriginalExtension() !== 'zip') {
            $this->addFlash('error', 'back.backup.flash.upload_not_zip');
            return $this->redirectToRoute('app_back_backup_index');
        }

        try {
            $filename = $this->backupService->importAndRestore($file);
            $this->addFlash('success', 'back.backup.flash.upload_restored');
        } catch (\Throwable $e) {
            $this->addFlash('error', 'back.backup.flash.error');
        }

        return $this->redirectToRoute('app_back_backup_index');
    }

    /**
     * @brief Deletes a backup archive.
     *
     * @param Request $request
     * @param string $filename The backup filename.
     * @return Response
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function delete(Request $request, string $filename): Response
    {
        if (!$this->isCsrfTokenValid('backup_delete_' . $filename, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_back_backup_index');
        }

        try {
            $this->backupService->deleteBackup($filename);
            $this->addFlash('success', 'back.backup.flash.deleted');
        } catch (\Throwable $e) {
            $this->addFlash('error', 'back.backup.flash.error');
        }

        return $this->redirectToRoute('app_back_backup_index');
    }
}
