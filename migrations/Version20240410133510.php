<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240410133510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__groupes AS SELECT id, responsable_id, departement_id, nom, acronyme FROM groupes');
        $this->addSql('DROP TABLE groupes');
        $this->addSql('CREATE TABLE groupes (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, responsable_id INTEGER DEFAULT NULL, departement_id INTEGER DEFAULT NULL, res_stockage_work_id INTEGER DEFAULT NULL, nom VARCHAR(32) NOT NULL, acronyme VARCHAR(8) DEFAULT NULL, CONSTRAINT FK_576366D953C59D72 FOREIGN KEY (responsable_id) REFERENCES employe (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_576366D9CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_576366D9A4512571 FOREIGN KEY (res_stockage_work_id) REFERENCES res_stockage_work (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO groupes (id, responsable_id, departement_id, nom, acronyme) SELECT id, responsable_id, departement_id, nom, acronyme FROM __temp__groupes');
        $this->addSql('DROP TABLE __temp__groupes');
        $this->addSql('CREATE INDEX IDX_576366D9CCF9E01E ON groupes (departement_id)');
        $this->addSql('CREATE INDEX IDX_576366D953C59D72 ON groupes (responsable_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_576366D9A4512571 ON groupes (res_stockage_work_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__res_stockage_work AS SELECT id, nom, path FROM res_stockage_work');
        $this->addSql('DROP TABLE res_stockage_work');
        $this->addSql('CREATE TABLE res_stockage_work (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(40) NOT NULL, path VARCHAR(64) DEFAULT NULL)');
        $this->addSql('INSERT INTO res_stockage_work (id, nom, path) SELECT id, nom, path FROM __temp__res_stockage_work');
        $this->addSql('DROP TABLE __temp__res_stockage_work');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__groupes AS SELECT id, responsable_id, departement_id, nom, acronyme FROM groupes');
        $this->addSql('DROP TABLE groupes');
        $this->addSql('CREATE TABLE groupes (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, responsable_id INTEGER DEFAULT NULL, departement_id INTEGER DEFAULT NULL, nom VARCHAR(32) NOT NULL, acronyme VARCHAR(8) DEFAULT NULL, CONSTRAINT FK_576366D953C59D72 FOREIGN KEY (responsable_id) REFERENCES employe (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_576366D9CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO groupes (id, responsable_id, departement_id, nom, acronyme) SELECT id, responsable_id, departement_id, nom, acronyme FROM __temp__groupes');
        $this->addSql('DROP TABLE __temp__groupes');
        $this->addSql('CREATE INDEX IDX_576366D953C59D72 ON groupes (responsable_id)');
        $this->addSql('CREATE INDEX IDX_576366D9CCF9E01E ON groupes (departement_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__res_stockage_work AS SELECT id, nom, path FROM res_stockage_work');
        $this->addSql('DROP TABLE res_stockage_work');
        $this->addSql('CREATE TABLE res_stockage_work (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, res_stockage_work_id INTEGER DEFAULT NULL, nom VARCHAR(40) NOT NULL, path VARCHAR(64) DEFAULT NULL, CONSTRAINT FK_9959FB32A4512571 FOREIGN KEY (res_stockage_work_id) REFERENCES res_stockage_work (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO res_stockage_work (id, nom, path) SELECT id, nom, path FROM __temp__res_stockage_work');
        $this->addSql('DROP TABLE __temp__res_stockage_work');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9959FB32A4512571 ON res_stockage_work (res_stockage_work_id)');
    }
}
