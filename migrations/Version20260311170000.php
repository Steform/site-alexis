<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Insert sample avis for StuSlider demo.
 *
 * @author Stephane H.
 * @created 2026-03-11
 *
 * @inputs  None
 * @outputs Sample avis in database for slider display
 */
final class Version20260311170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insert sample avis for StuSlider demo';
    }

    public function up(Schema $schema): void
    {
        $avis = [
            ['Travail toujours impeccable, on peut avoir confiance !', 'Pierre M.', 5, '2025-02-15'],
            ['Réparation rapide et soignée. Je recommande vivement.', 'Marie L.', 5, '2025-03-01'],
            ['Équipe professionnelle, devis clair et honnête.', 'Jean D.', 5, '2025-03-08'],
        ];

        foreach ($avis as $a) {
            $this->connection->executeStatement(
                'INSERT INTO avis (texte, auteur, note, date) VALUES (?, ?, ?, ?)',
                [$a[0], $a[1], $a[2], $a[3]],
                [\Doctrine\DBAL\ParameterType::STRING, \Doctrine\DBAL\ParameterType::STRING, \Doctrine\DBAL\ParameterType::INTEGER, \Doctrine\DBAL\ParameterType::STRING]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->connection->executeStatement(
            "DELETE FROM avis WHERE auteur IN ('Pierre M.', 'Marie L.', 'Jean D.')"
        );
    }
}
