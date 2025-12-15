<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250530095614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE content DROP FOREIGN KEY FK_FEC530A9A21CC0F6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE proposition DROP FOREIGN KEY FK_C7CDC353A21CC0F6
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE terme (id INT AUTO_INCREMENT NOT NULL, approved_by_id INT DEFAULT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, image_id INT DEFAULT NULL, statut VARCHAR(255) NOT NULL, approved_at DATETIME DEFAULT NULL, INDEX IDX_7C768A202D234F6A (approved_by_id), INDEX IDX_7C768A20B03A8386 (created_by_id), INDEX IDX_7C768A20896DBBDE (updated_by_id), UNIQUE INDEX UNIQ_7C768A203DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terme ADD CONSTRAINT FK_7C768A202D234F6A FOREIGN KEY (approved_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terme ADD CONSTRAINT FK_7C768A20B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terme ADD CONSTRAINT FK_7C768A20896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terme ADD CONSTRAINT FK_7C768A203DA5256D FOREIGN KEY (image_id) REFERENCES file (id)
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
            ALTER TABLE glossaire DROP FOREIGN KEY FK_F3387A212D234F6A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE glossaire
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_FEC530A9A21CC0F6 ON content
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content CHANGE glossaire_id terme_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content ADD CONSTRAINT FK_FEC530A926062764 FOREIGN KEY (terme_id) REFERENCES terme (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FEC530A926062764 ON content (terme_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_C7CDC353A21CC0F6 ON proposition
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE proposition CHANGE glossaire_id terme_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE proposition ADD CONSTRAINT FK_C7CDC35326062764 FOREIGN KEY (terme_id) REFERENCES terme (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_C7CDC35326062764 ON proposition (terme_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE statut statut VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE content DROP FOREIGN KEY FK_FEC530A926062764
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE proposition DROP FOREIGN KEY FK_C7CDC35326062764
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE glossaire (id INT AUTO_INCREMENT NOT NULL, approved_by_id INT DEFAULT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, image_id INT DEFAULT NULL, statut VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_F3387A212D234F6A (approved_by_id), INDEX IDX_F3387A21896DBBDE (updated_by_id), INDEX IDX_F3387A21B03A8386 (created_by_id), UNIQUE INDEX UNIQ_F3387A213DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE glossaire ADD CONSTRAINT FK_F3387A21B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE glossaire ADD CONSTRAINT FK_F3387A21896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE glossaire ADD CONSTRAINT FK_F3387A213DA5256D FOREIGN KEY (image_id) REFERENCES file (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE glossaire ADD CONSTRAINT FK_F3387A212D234F6A FOREIGN KEY (approved_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terme DROP FOREIGN KEY FK_7C768A202D234F6A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terme DROP FOREIGN KEY FK_7C768A20B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terme DROP FOREIGN KEY FK_7C768A20896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terme DROP FOREIGN KEY FK_7C768A203DA5256D
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE terme
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` CHANGE statut statut VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_FEC530A926062764 ON content
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content CHANGE terme_id glossaire_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content ADD CONSTRAINT FK_FEC530A9A21CC0F6 FOREIGN KEY (glossaire_id) REFERENCES glossaire (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FEC530A9A21CC0F6 ON content (glossaire_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_C7CDC35326062764 ON proposition
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE proposition CHANGE terme_id glossaire_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE proposition ADD CONSTRAINT FK_C7CDC353A21CC0F6 FOREIGN KEY (glossaire_id) REFERENCES glossaire (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_C7CDC353A21CC0F6 ON proposition (glossaire_id)
        SQL);
    }
}
