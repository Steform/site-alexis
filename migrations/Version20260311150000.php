<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Rename ROLE_ALEXIS to ROLE_PROPRIETAIRE in user roles.
 *
 * @author Stephane H.
 * @created 2026-03-11
 */
final class Version20260311150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename ROLE_ALEXIS to ROLE_PROPRIETAIRE';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE `user` SET roles = REPLACE(roles, '\"ROLE_ALEXIS\"', '\"ROLE_PROPRIETAIRE\"') WHERE roles LIKE '%ROLE_ALEXIS%'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE `user` SET roles = REPLACE(roles, '\"ROLE_PROPRIETAIRE\"', '\"ROLE_ALEXIS\"') WHERE roles LIKE '%ROLE_PROPRIETAIRE%'");
    }
}
