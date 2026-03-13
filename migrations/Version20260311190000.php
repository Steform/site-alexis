<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add split morning/afternoon hours and DE comment to horaires.
 *
 * @author Stephane H.
 * @created 2026-03-12
 */
final class Version20260311190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add heure_debut_matin/heure_fin_matin, heure_debut_apres_midi/heure_fin_apres_midi and commentaire_de to horaires';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE horaires ADD heure_debut_matin TIME DEFAULT NULL, ADD heure_fin_matin TIME DEFAULT NULL, ADD heure_debut_apres_midi TIME DEFAULT NULL, ADD heure_fin_apres_midi TIME DEFAULT NULL, ADD commentaire_de LONGTEXT DEFAULT NULL');
        // Initialize morning slot with existing single range as a starting point
        $this->addSql('UPDATE horaires SET heure_debut_matin = heure_debut, heure_fin_matin = heure_fin WHERE heure_debut IS NOT NULL AND heure_fin IS NOT NULL');
        // Copy FR comment into DE comment as default
        $this->addSql('UPDATE horaires SET commentaire_de = commentaire WHERE commentaire IS NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE horaires DROP heure_debut_matin, DROP heure_fin_matin, DROP heure_debut_apres_midi, DROP heure_fin_apres_midi, DROP commentaire_de');
    }
}

