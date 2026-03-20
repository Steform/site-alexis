<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Creates devis_type_carburant table and seeds initial fuel types.
 *
 * @date 2026-03-19
 * @author Stephane H.
 */
final class Version20260319200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create devis_type_carburant table and seed initial fuel types.';
    }

    public function up(Schema $schema): void
    {
        $this->connection->executeStatement('CREATE TABLE `devis_type_carburant` (
            id INT AUTO_INCREMENT NOT NULL,
            code VARCHAR(80) NOT NULL,
            label VARCHAR(255) NOT NULL,
            label_de VARCHAR(255) DEFAULT NULL,
            ordre SMALLINT NOT NULL DEFAULT 0,
            actif TINYINT(1) NOT NULL DEFAULT 1,
            UNIQUE INDEX UNIQ_devis_carburant_code (code),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $types = [
            ['diesel', 'Diesel', 'Diesel', 1],
            ['essence', 'Essence', 'Benzin', 2],
            ['electrique', 'Électrique', 'Elektro', 3],
            ['hybride', 'Hybride', 'Hybrid', 4],
            ['hybride_rechargeable', 'Hybride rechargeable', 'Plug-in-Hybrid', 5],
            ['e85', 'E85', 'E85', 6],
            ['gpl', 'GPL', 'LPG', 7],
        ];

        foreach ($types as $t) {
            $this->connection->executeStatement(
                'INSERT INTO devis_type_carburant (code, label, label_de, ordre, actif) VALUES (?, ?, ?, ?, 1)',
                [$t[0], $t[1], $t[2], $t[3]],
                [ParameterType::STRING, ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->connection->executeStatement('DROP TABLE `devis_type_carburant`');
    }
}
