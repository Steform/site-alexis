<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260318000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add social network URLs to coordinates.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE coordinates ADD facebook_url VARCHAR(255) DEFAULT NULL, ADD instagram_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE coordinates DROP facebook_url, DROP instagram_url');
    }
}

