<?php

namespace App\Controller\Back;

use App\Service\AdminAuditActions;
use App\Service\AdminAuditLogger;
use App\Service\BackupService;
use App\Service\SiteResetService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        private readonly SiteResetService $siteResetService,
        private readonly AdminAuditLogger $adminAuditLogger,
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
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
            'backup_format_version' => BackupService::BACKUP_ARCHIVE_FORMAT_VERSION,
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
            $this->adminAuditLogger->log(AdminAuditActions::BACKUP_CREATE, [
                'filename' => $filename,
            ], $this->getUser());
            $this->addFlash('success', 'back.backup.flash.created');
        } catch (\Throwable $e) {
            $this->logger->error('Backup create failed.', ['exception' => $e]);
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
            $this->adminAuditLogger->log(AdminAuditActions::BACKUP_RESTORE, [
                'filename' => $filename,
            ], $this->getUser());
            $this->addFlash('success', 'back.backup.flash.restored');
        } catch (\Throwable $e) {
            $this->logger->error('Backup restore failed.', [
                'filename' => $filename,
                'exception' => $e,
            ]);
            $this->addBackupRestoreErrorFlash($e);
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
            $this->adminAuditLogger->log(AdminAuditActions::BACKUP_UPLOAD_RESTORE, [
                'filename' => $filename,
            ], $this->getUser());
            $this->addFlash('success', 'back.backup.flash.upload_restored');
        } catch (\Throwable $e) {
            $this->logger->error('Backup upload-restore failed.', ['exception' => $e]);
            $this->addBackupRestoreErrorFlash($e);
        }

        return $this->redirectToRoute('app_back_backup_index');
    }

    /**
     * @brief Creates a fresh backup then deletes all other backup archives (keeps the new snapshot only).
     *
     * @param Request $request The HTTP request.
     * @return Response Redirect to the backup list.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function pruneBackups(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('backup_prune', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_back_backup_index');
        }

        try {
            $result = $this->backupService->pruneBackupsKeepLatest();
            $this->adminAuditLogger->log(AdminAuditActions::BACKUP_DELETE_ALL, [
                'preserved_filename' => $result['preserved_filename'],
                'deleted_count' => $result['deleted_count'],
            ], $this->getUser());
            $this->addFlash(
                'success',
                $this->translator->trans('back.backup.flash.prune_success', [
                    '%filename%' => $result['preserved_filename'],
                    '%count%' => (string) $result['deleted_count'],
                ], 'back')
            );
        } catch (\Throwable $e) {
            $this->logger->error('Backup prune failed.', ['exception' => $e]);
            $this->addFlash('error', 'back.backup.flash.error');
        }

        return $this->redirectToRoute('app_back_backup_index');
    }

    /**
     * @brief Resets the site to setup state: optional emergency ZIP, purge archives, truncate DB (except migrations), clear uploads, logout.
     *
     * @param Request $request The HTTP request.
     * @return Response Redirect to public setup wizard.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function resetSite(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('backup_site_reset', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_back_backup_index');
        }

        $user = $this->getUser();
        $adminLabel = $user instanceof UserInterface ? $user->getUserIdentifier() : 'unknown';

        try {
            $createEmergency = $request->request->has('emergency_backup');
            $this->logger->notice('Site reset to setup state started.', [
                'admin' => $adminLabel,
                'emergency_backup' => $createEmergency,
            ]);

            $result = $this->siteResetService->resetToSetupState($createEmergency);

            $this->logger->notice('Site reset to setup state completed.', [
                'admin' => $adminLabel,
                'preserved_backup' => $result['preserved_backup'],
                'deleted_zip_count' => $result['deleted_zip_count'],
            ]);

            $request->getSession()->invalidate();
        } catch (\Throwable $e) {
            $this->logger->error('Site reset failed.', ['exception' => $e, 'admin' => $adminLabel]);
            $this->addFlash('error', 'back.backup.flash.reset_error');

            return $this->redirectToRoute('app_back_backup_index');
        }

        return $this->redirectToRoute('app_setup');
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
            $this->adminAuditLogger->log(AdminAuditActions::BACKUP_DELETE, [
                'filename' => $filename,
            ], $this->getUser());
            $this->addFlash('success', 'back.backup.flash.deleted');
        } catch (\Throwable $e) {
            $this->logger->error('Backup delete failed.', ['filename' => $filename, 'exception' => $e]);
            $this->addFlash('error', 'back.backup.flash.error');
        }

        return $this->redirectToRoute('app_back_backup_index');
    }

    /**
     * @brief Adds an error flash for backup restore failures, with detail when manifest validation fails.
     *
     * @param \Throwable $e The exception thrown during restore.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function addBackupRestoreErrorFlash(\Throwable $e): void
    {
        $msg = $e->getMessage();
        if ($this->isBackupManifestRelatedErrorMessage($msg)) {
            $this->addFlash('error', $this->translator->trans('back.backup.flash.manifest_invalid', ['%details%' => $msg], 'back'));

            return;
        }
        $this->addFlash('error', $this->translator->trans('back.backup.flash.restore_failed_detail', ['%details%' => $msg], 'back'));
    }

    /**
     * @brief Returns whether the exception message indicates manifest or content validation failure.
     *
     * @param string $message The exception message.
     * @return bool True if the user-facing manifest message should be shown.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function isBackupManifestRelatedErrorMessage(string $message): bool
    {
        $lower = strtolower($message);

        return str_contains($lower, 'manifest')
            || str_contains($lower, 'mismatch')
            || str_contains($lower, 'json');
    }
}
