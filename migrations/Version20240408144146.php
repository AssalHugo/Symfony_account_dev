<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240408144146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__groupes_sys AS SELECT id, nom FROM groupes_sys');
        $this->addSql('DROP TABLE groupes_sys');
        $this->addSql('CREATE TABLE groupes_sys (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, nom VARCHAR(32) NOT NULL, CONSTRAINT FK_7FB5496AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO groupes_sys (id, nom) SELECT id, nom FROM __temp__groupes_sys');
        $this->addSql('DROP TABLE __temp__groupes_sys');
        $this->addSql('CREATE INDEX IDX_7FB5496AA76ED395 ON groupes_sys (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__groupes_sys AS SELECT id, nom FROM groupes_sys');
        $this->addSql('DROP TABLE groupes_sys');
        $this->addSql('CREATE TABLE groupes_sys (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, groupe_id INTEGER NOT NULL, nom VARCHAR(32) NOT NULL, CONSTRAINT FK_7FB5496A7A45358C FOREIGN KEY (groupe_id) REFERENCES groupes (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO groupes_sys (id, nom) SELECT id, nom FROM __temp__groupes_sys');
        $this->addSql('DROP TABLE __temp__groupes_sys');
        $this->addSql('CREATE INDEX IDX_7FB5496A7A45358C ON groupes_sys (groupe_id)');
    }
}
