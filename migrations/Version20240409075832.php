<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240409075832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE res_stockage_work ADD COLUMN path VARCHAR(64) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__res_stockage_work AS SELECT id, groupe_sys_id, nom FROM res_stockage_work');
        $this->addSql('DROP TABLE res_stockage_work');
        $this->addSql('CREATE TABLE res_stockage_work (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, groupe_sys_id INTEGER DEFAULT NULL, nom VARCHAR(40) NOT NULL, CONSTRAINT FK_9959FB324302B318 FOREIGN KEY (groupe_sys_id) REFERENCES groupes_sys (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO res_stockage_work (id, groupe_sys_id, nom) SELECT id, groupe_sys_id, nom FROM __temp__res_stockage_work');
        $this->addSql('DROP TABLE __temp__res_stockage_work');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9959FB324302B318 ON res_stockage_work (groupe_sys_id)');
    }
}
