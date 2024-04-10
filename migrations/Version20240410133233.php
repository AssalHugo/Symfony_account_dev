<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240410133233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__res_stockage_work AS SELECT id, groupe_id, nom, path FROM res_stockage_work');
        $this->addSql('DROP TABLE res_stockage_work');
        $this->addSql('CREATE TABLE res_stockage_work (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, res_stockage_work_id INTEGER DEFAULT NULL, nom VARCHAR(40) NOT NULL, path VARCHAR(64) DEFAULT NULL, CONSTRAINT FK_9959FB32A4512571 FOREIGN KEY (res_stockage_work_id) REFERENCES res_stockage_work (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO res_stockage_work (id, res_stockage_work_id, nom, path) SELECT id, groupe_id, nom, path FROM __temp__res_stockage_work');
        $this->addSql('DROP TABLE __temp__res_stockage_work');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9959FB32A4512571 ON res_stockage_work (res_stockage_work_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__res_stockage_work AS SELECT id, res_stockage_work_id, nom, path FROM res_stockage_work');
        $this->addSql('DROP TABLE res_stockage_work');
        $this->addSql('CREATE TABLE res_stockage_work (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, groupe_id INTEGER DEFAULT NULL, nom VARCHAR(40) NOT NULL, path VARCHAR(64) DEFAULT NULL, CONSTRAINT FK_9959FB327A45358C FOREIGN KEY (groupe_id) REFERENCES groupes (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO res_stockage_work (id, groupe_id, nom, path) SELECT id, res_stockage_work_id, nom, path FROM __temp__res_stockage_work');
        $this->addSql('DROP TABLE __temp__res_stockage_work');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9959FB327A45358C ON res_stockage_work (groupe_id)');
    }
}
