<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251119125753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE content ADD synonyme_fr VARCHAR(255) DEFAULT NULL, ADD synonyme_ar VARCHAR(255) DEFAULT NULL, ADD synonyme_dr VARCHAR(255) DEFAULT NULL, ADD domaine_applications_fr VARCHAR(255) DEFAULT NULL, ADD domaine_applications_ar VARCHAR(255) DEFAULT NULL, ADD domaine_applications_dr VARCHAR(255) DEFAULT NULL, ADD categorie_fr VARCHAR(255) DEFAULT NULL, ADD categorie_ar VARCHAR(255) DEFAULT NULL, ADD categorie_dr VARCHAR(255) DEFAULT NULL, ADD source_fr VARCHAR(255) DEFAULT NULL, ADD source_ar VARCHAR(255) DEFAULT NULL, ADD source_dr VARCHAR(255) DEFAULT NULL, ADD categorie_grammaticale_fr VARCHAR(255) DEFAULT NULL, ADD categorie_grammaticale_ar VARCHAR(255) DEFAULT NULL, ADD categorie_grammaticale_dr VARCHAR(255) DEFAULT NULL, ADD relation_terminologique_fr VARCHAR(255) DEFAULT NULL, ADD relation_terminologique_ar VARCHAR(255) DEFAULT NULL, ADD relation_terminologique_dr VARCHAR(255) DEFAULT NULL, ADD equivalent_anglais_fr VARCHAR(255) DEFAULT NULL, ADD equivalent_anglais_ar VARCHAR(255) DEFAULT NULL, ADD equivalent_anglais_dr VARCHAR(255) DEFAULT NULL, ADD equivalent_espagnol_fr VARCHAR(255) DEFAULT NULL, ADD equivalent_espagnol_ar VARCHAR(255) DEFAULT NULL, ADD equivalent_espagnol_dr VARCHAR(255) DEFAULT NULL, ADD idiome_fr VARCHAR(255) DEFAULT NULL, ADD idiome_ar VARCHAR(255) DEFAULT NULL, ADD idiome_dr VARCHAR(255) DEFAULT NULL, ADD usage_metaphorique_fr VARCHAR(255) DEFAULT NULL, ADD usage_metaphorique_ar VARCHAR(255) DEFAULT NULL, ADD usage_metaphorique_dr VARCHAR(255) DEFAULT NULL, ADD recit_vie_fr VARCHAR(500) DEFAULT NULL, ADD recit_vie_ar VARCHAR(500) DEFAULT NULL, ADD recit_vie_dr VARCHAR(500) DEFAULT NULL, ADD liens_hypertexte_fr VARCHAR(255) DEFAULT NULL, ADD liens_hypertexte_ar VARCHAR(255) DEFAULT NULL, ADD liens_hypertexte_dr VARCHAR(255) DEFAULT NULL, CHANGE titre_fr titre_fr VARCHAR(255) DEFAULT NULL, CHANGE titre_ar titre_ar VARCHAR(255) DEFAULT NULL, CHANGE titre_dr titre_dr VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE content DROP synonyme_fr, DROP synonyme_ar, DROP synonyme_dr, DROP domaine_applications_fr, DROP domaine_applications_ar, DROP domaine_applications_dr, DROP categorie_fr, DROP categorie_ar, DROP categorie_dr, DROP source_fr, DROP source_ar, DROP source_dr, DROP categorie_grammaticale_fr, DROP categorie_grammaticale_ar, DROP categorie_grammaticale_dr, DROP relation_terminologique_fr, DROP relation_terminologique_ar, DROP relation_terminologique_dr, DROP equivalent_anglais_fr, DROP equivalent_anglais_ar, DROP equivalent_anglais_dr, DROP equivalent_espagnol_fr, DROP equivalent_espagnol_ar, DROP equivalent_espagnol_dr, DROP idiome_fr, DROP idiome_ar, DROP idiome_dr, DROP usage_metaphorique_fr, DROP usage_metaphorique_ar, DROP usage_metaphorique_dr, DROP recit_vie_fr, DROP recit_vie_ar, DROP recit_vie_dr, DROP liens_hypertexte_fr, DROP liens_hypertexte_ar, DROP liens_hypertexte_dr, CHANGE titre_fr titre_fr VARCHAR(255) NOT NULL, CHANGE titre_ar titre_ar VARCHAR(255) NOT NULL, CHANGE titre_dr titre_dr VARCHAR(255) NOT NULL
        SQL);
    }
}
