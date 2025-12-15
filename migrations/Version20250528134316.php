<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528134316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, approved_by_id INT DEFAULT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, image_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, statut VARCHAR(255) NOT NULL, INDEX IDX_23A0E662D234F6A (approved_by_id), INDEX IDX_23A0E66B03A8386 (created_by_id), INDEX IDX_23A0E66896DBBDE (updated_by_id), UNIQUE INDEX UNIQ_23A0E663DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE article_categorie (article_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_934886107294869C (article_id), INDEX IDX_93488610BCF5E72D (categorie_id), PRIMARY KEY(article_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_497DD634B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE content (id INT AUTO_INCREMENT NOT NULL, glossaire_id INT DEFAULT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, proposition_id INT DEFAULT NULL, titre_fr VARCHAR(255) NOT NULL, description_fr VARCHAR(300) DEFAULT NULL, titre_ar VARCHAR(255) NOT NULL, description_ar VARCHAR(300) DEFAULT NULL, titre_dr VARCHAR(255) NOT NULL, description_dr VARCHAR(300) DEFAULT NULL, INDEX IDX_FEC530A9A21CC0F6 (glossaire_id), INDEX IDX_FEC530A9B03A8386 (created_by_id), INDEX IDX_FEC530A9896DBBDE (updated_by_id), INDEX IDX_FEC530A9DB96F9E (proposition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, size VARCHAR(255) NOT NULL, extension VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE glossaire (id INT AUTO_INCREMENT NOT NULL, approved_by_id INT DEFAULT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, image_id INT DEFAULT NULL, statut VARCHAR(255) NOT NULL, INDEX IDX_F3387A212D234F6A (approved_by_id), INDEX IDX_F3387A21B03A8386 (created_by_id), INDEX IDX_F3387A21896DBBDE (updated_by_id), UNIQUE INDEX UNIQ_F3387A213DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE proposition (id INT AUTO_INCREMENT NOT NULL, action_by_id INT DEFAULT NULL, created_by_id INT NOT NULL, image_id INT DEFAULT NULL, glossaire_id INT DEFAULT NULL, statut VARCHAR(255) NOT NULL, INDEX IDX_C7CDC3534D29ECB8 (action_by_id), INDEX IDX_C7CDC353B03A8386 (created_by_id), UNIQUE INDEX UNIQ_C7CDC3533DA5256D (image_id), UNIQUE INDEX UNIQ_C7CDC353A21CC0F6 (glossaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT FK_23A0E662D234F6A FOREIGN KEY (approved_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT FK_23A0E66B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT FK_23A0E66896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT FK_23A0E663DA5256D FOREIGN KEY (image_id) REFERENCES file (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article_categorie ADD CONSTRAINT FK_934886107294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article_categorie ADD CONSTRAINT FK_93488610BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE categorie ADD CONSTRAINT FK_497DD634B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content ADD CONSTRAINT FK_FEC530A9A21CC0F6 FOREIGN KEY (glossaire_id) REFERENCES glossaire (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content ADD CONSTRAINT FK_FEC530A9B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content ADD CONSTRAINT FK_FEC530A9896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content ADD CONSTRAINT FK_FEC530A9DB96F9E FOREIGN KEY (proposition_id) REFERENCES proposition (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE glossaire ADD CONSTRAINT FK_F3387A212D234F6A FOREIGN KEY (approved_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE glossaire ADD CONSTRAINT FK_F3387A21B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE glossaire ADD CONSTRAINT FK_F3387A21896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE glossaire ADD CONSTRAINT FK_F3387A213DA5256D FOREIGN KEY (image_id) REFERENCES file (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE proposition ADD CONSTRAINT FK_C7CDC3534D29ECB8 FOREIGN KEY (action_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE proposition ADD CONSTRAINT FK_C7CDC353B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE proposition ADD CONSTRAINT FK_C7CDC3533DA5256D FOREIGN KEY (image_id) REFERENCES file (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE proposition ADD CONSTRAINT FK_C7CDC353A21CC0F6 FOREIGN KEY (glossaire_id) REFERENCES glossaire (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD nom VARCHAR(255) NOT NULL, ADD prenom VARCHAR(255) NOT NULL, ADD statut VARCHAR(255) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP FOREIGN KEY FK_23A0E662D234F6A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP FOREIGN KEY FK_23A0E66B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP FOREIGN KEY FK_23A0E66896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP FOREIGN KEY FK_23A0E663DA5256D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article_categorie DROP FOREIGN KEY FK_934886107294869C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article_categorie DROP FOREIGN KEY FK_93488610BCF5E72D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE categorie DROP FOREIGN KEY FK_497DD634B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content DROP FOREIGN KEY FK_FEC530A9A21CC0F6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content DROP FOREIGN KEY FK_FEC530A9B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content DROP FOREIGN KEY FK_FEC530A9896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content DROP FOREIGN KEY FK_FEC530A9DB96F9E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE glossaire DROP FOREIGN KEY FK_F3387A212D234F6A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE glossaire DROP FOREIGN KEY FK_F3387A21B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE glossaire DROP FOREIGN KEY FK_F3387A21896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE glossaire DROP FOREIGN KEY FK_F3387A213DA5256D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE proposition DROP FOREIGN KEY FK_C7CDC3534D29ECB8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE proposition DROP FOREIGN KEY FK_C7CDC353B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE proposition DROP FOREIGN KEY FK_C7CDC3533DA5256D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE proposition DROP FOREIGN KEY FK_C7CDC353A21CC0F6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE article
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE article_categorie
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE categorie
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE content
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE file
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE glossaire
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE proposition
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` DROP nom, DROP prenom, DROP statut
        SQL);
    }
}
