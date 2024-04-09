<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240409092917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE res_stockages_home_stockages_mesures_home (res_stockages_home_id INTEGER NOT NULL, stockages_mesures_home_id INTEGER NOT NULL, PRIMARY KEY(res_stockages_home_id, stockages_mesures_home_id), CONSTRAINT FK_A379D2E2E5C77D6 FOREIGN KEY (res_stockages_home_id) REFERENCES res_stockages_home (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A379D2E23FFCA4B FOREIGN KEY (stockages_mesures_home_id) REFERENCES stockages_mesures_home (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A379D2E2E5C77D6 ON res_stockages_home_stockages_mesures_home (res_stockages_home_id)');
        $this->addSql('CREATE INDEX IDX_A379D2E23FFCA4B ON res_stockages_home_stockages_mesures_home (stockages_mesures_home_id)');
        $this->addSql('DROP TABLE res_stockages_home_stockage_mesures_home');
        $this->addSql('DROP TABLE stockage_mesures_home');
        $this->addSql('ALTER TABLE stockages_mesures_home ADD COLUMN date_mesure DATE NOT NULL');
        $this->addSql('ALTER TABLE stockages_mesures_home ADD COLUMN valeur_use INTEGER NOT NULL');
        $this->addSql('ALTER TABLE stockages_mesures_home ADD COLUMN valeur_max INTEGER NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE res_stockages_home_stockage_mesures_home (res_stockages_home_id INTEGER NOT NULL, stockage_mesures_home_id INTEGER NOT NULL, PRIMARY KEY(res_stockages_home_id, stockage_mesures_home_id), CONSTRAINT FK_D55B6123E5C77D6 FOREIGN KEY (res_stockages_home_id) REFERENCES res_stockages_home (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D55B6123D656D371 FOREIGN KEY (stockage_mesures_home_id) REFERENCES stockage_mesures_home (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D55B6123D656D371 ON res_stockages_home_stockage_mesures_home (stockage_mesures_home_id)');
        $this->addSql('CREATE INDEX IDX_D55B6123E5C77D6 ON res_stockages_home_stockage_mesures_home (res_stockages_home_id)');
        $this->addSql('CREATE TABLE stockage_mesures_home (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date_mesure DATE NOT NULL, valeur_use INTEGER NOT NULL, valeur_max INTEGER NOT NULL)');
        $this->addSql('DROP TABLE res_stockages_home_stockages_mesures_home');
        $this->addSql('CREATE TEMPORARY TABLE __temp__stockages_mesures_home AS SELECT id, periode_id FROM stockages_mesures_home');
        $this->addSql('DROP TABLE stockages_mesures_home');
        $this->addSql('CREATE TABLE stockages_mesures_home (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, periode_id INTEGER DEFAULT NULL, CONSTRAINT FK_5800368AF384C1CF FOREIGN KEY (periode_id) REFERENCES periode (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO stockages_mesures_home (id, periode_id) SELECT id, periode_id FROM __temp__stockages_mesures_home');
        $this->addSql('DROP TABLE __temp__stockages_mesures_home');
        $this->addSql('CREATE INDEX IDX_5800368AF384C1CF ON stockages_mesures_home (periode_id)');
    }
}
