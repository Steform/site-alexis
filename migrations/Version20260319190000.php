<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Creates devis_type_prestation table and seeds initial types.
 *
 * @date 2026-03-19
 * @author Stephane H.
 */
final class Version20260319190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create devis_type_prestation table and seed initial quote service types.';
    }

    public function up(Schema $schema): void
    {
        $this->connection->executeStatement('CREATE TABLE `devis_type_prestation` (
            id INT AUTO_INCREMENT NOT NULL,
            code VARCHAR(80) NOT NULL,
            label VARCHAR(255) NOT NULL,
            label_de VARCHAR(255) DEFAULT NULL,
            ordre SMALLINT NOT NULL DEFAULT 0,
            actif TINYINT(1) NOT NULL DEFAULT 1,
            UNIQUE INDEX UNIQ_devis_type_code (code),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $types = [
            ['reparation_carrosserie', 'Réparation carrosserie', 'Karosseriereparatur', 1],
            ['peinture', 'Peinture', 'Lackierung', 2],
            ['debosselage', 'Débosselage', 'Ausbeulen', 3],
            ['pare_brise', 'Pare-brise / Vitrages', 'Scheiben / Verglasung', 4],
            ['entretien', 'Entretien / Mécanique', 'Wartung / Mechanik', 5],
            ['autre', 'Autre', 'Sonstiges', 6],
        ];

        foreach ($types as $t) {
            $this->connection->executeStatement(
                'INSERT INTO devis_type_prestation (code, label, label_de, ordre, actif) VALUES (?, ?, ?, ?, 1)',
                [$t[0], $t[1], $t[2], $t[3]],
                [ParameterType::STRING, ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->connection->executeStatement('DROP TABLE `devis_type_prestation`');
    }
}
