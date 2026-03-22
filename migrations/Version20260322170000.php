<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Adds service_process_step table and migrates legacy CMS process.step1–4 blocks.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
final class Version20260322170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create service_process_step; import from content_block process.step*; remove legacy blocks.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE service_process_step (
            id INT AUTO_INCREMENT NOT NULL,
            service_id INT NOT NULL,
            position SMALLINT NOT NULL,
            label_fr VARCHAR(500) NOT NULL,
            label_de VARCHAR(500) NOT NULL,
            INDEX IDX_SERVICE_PROCESS_SERVICE (service_id),
            INDEX idx_service_process_step_service_position (service_id, position),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_process_step ADD CONSTRAINT FK_SERVICE_PROCESS_SERVICE FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');

        $services = $this->connection->fetchAllAssociative('SELECT id, slug FROM service');
        foreach ($services as $row) {
            $serviceId = (int) $row['id'];
            $slug = (string) $row['slug'];
            $pageName = 'service_' . $slug;

            for ($i = 1; $i <= 4; ++$i) {
                $blockKey = 'process.step' . $i;
                $frRaw = $this->connection->fetchOne(
                    'SELECT value FROM content_block WHERE page_name = ? AND block_key = ? AND locale = ?',
                    [$pageName, $blockKey, 'fr']
                );
                $deRaw = $this->connection->fetchOne(
                    'SELECT value FROM content_block WHERE page_name = ? AND block_key = ? AND locale = ?',
                    [$pageName, $blockKey, 'de']
                );
                $fr = trim($frRaw === false ? '' : (string) $frRaw);
                $de = trim($deRaw === false ? '' : (string) $deRaw);
                if ($fr === '' && $de === '') {
                    continue;
                }

                $this->connection->executeStatement(
                    'INSERT INTO service_process_step (service_id, position, label_fr, label_de) VALUES (?, ?, ?, ?)',
                    [$serviceId, $i - 1, $fr, $de],
                    [ParameterType::INTEGER, ParameterType::INTEGER, ParameterType::STRING, ParameterType::STRING]
                );
            }
        }

        $this->addSql("DELETE FROM content_block WHERE block_key IN ('process.step1', 'process.step2', 'process.step3', 'process.step4') AND page_name LIKE 'service_%'");
        $this->addSql("DELETE FROM content_block_history WHERE block_key IN ('process.step1', 'process.step2', 'process.step3', 'process.step4') AND page_name LIKE 'service_%'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE service_process_step DROP FOREIGN KEY FK_SERVICE_PROCESS_SERVICE');
        $this->addSql('DROP TABLE service_process_step');
    }
}
