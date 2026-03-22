<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Updates debosselage service title to "Débosselage sans peinture".
 *
 * @date 2026-03-21
 * @author Stephane H.
 */
final class Version20260321140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update debosselage service title to Débosselage sans peinture.';
    }

    public function up(Schema $schema): void
    {
        $this->connection->executeStatement(
            "UPDATE service SET titre = 'Débosselage sans peinture', titre_de = 'Beulenfrei ohne Lackierung' WHERE slug = 'debosselage'"
        );
    }

    public function down(Schema $schema): void
    {
        $this->connection->executeStatement(
            "UPDATE service SET titre = 'Débosselage', titre_de = 'Ausbeulen' WHERE slug = 'debosselage'"
        );
    }
}
