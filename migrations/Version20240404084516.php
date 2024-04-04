<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240404084516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__departement AS SELECT id, responsable_id, nom, acronyme FROM departement');
        $this->addSql('DROP TABLE departement');
        $this->addSql('CREATE TABLE departement (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, responsable_id INTEGER NOT NULL, nom VARCHAR(40) NOT NULL, acronyme VARCHAR(8) NOT NULL, CONSTRAINT FK_C1765B6353C59D72 FOREIGN KEY (responsable_id) REFERENCES employe (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO departement (id, responsable_id, nom, acronyme) SELECT id, responsable_id, nom, acronyme FROM __temp__departement');
        $this->addSql('DROP TABLE __temp__departement');
        $this->addSql('CREATE INDEX IDX_C1765B6353C59D72 ON departement (responsable_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__departement AS SELECT id, responsable_id, nom, acronyme FROM departement');
        $this->addSql('DROP TABLE departement');
        $this->addSql('CREATE TABLE departement (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, responsable_id INTEGER DEFAULT NULL, nom VARCHAR(40) NOT NULL, acronyme VARCHAR(8) NOT NULL, CONSTRAINT FK_C1765B6353C59D72 FOREIGN KEY (responsable_id) REFERENCES employe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO departement (id, responsable_id, nom, acronyme) SELECT id, responsable_id, nom, acronyme FROM __temp__departement');
        $this->addSql('DROP TABLE __temp__departement');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C1765B6353C59D72 ON departement (responsable_id)');
    }
}
