<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add DE text fields for avis, messages and gallery items.
 *
 * @author Stephane H.
 * @created 2026-03-12
 */
final class Version20260311180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add texte_de to avis, contenu_de to messages and titre_de/description_de to gallery_item';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE avis ADD texte_de LONGTEXT DEFAULT NULL');
        $this->addSql('UPDATE avis SET texte_de = texte WHERE texte IS NOT NULL');

        $this->addSql('ALTER TABLE messages ADD contenu_de LONGTEXT DEFAULT NULL');
        $this->addSql('UPDATE messages SET contenu_de = contenu WHERE contenu IS NOT NULL');

        $this->addSql('ALTER TABLE gallery_item ADD titre_de VARCHAR(255) DEFAULT NULL, ADD description_de LONGTEXT DEFAULT NULL');
        $this->addSql('UPDATE gallery_item SET titre_de = titre, description_de = description WHERE titre IS NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE avis DROP texte_de');
        $this->addSql('ALTER TABLE messages DROP contenu_de');
        $this->addSql('ALTER TABLE gallery_item DROP titre_de, DROP description_de');
    }
}

