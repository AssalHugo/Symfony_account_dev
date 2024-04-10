<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240409144059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__stockages_mesures_home AS SELECT id, periode_id, date_mesure, valeur_use, valeur_max FROM stockages_mesures_home');
        $this->addSql('DROP TABLE stockages_mesures_home');
        $this->addSql('CREATE TABLE stockages_mesures_home (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, periode_id INTEGER DEFAULT NULL, date_mesure DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , valeur_use INTEGER NOT NULL, valeur_max INTEGER NOT NULL, CONSTRAINT FK_5800368AF384C1CF FOREIGN KEY (periode_id) REFERENCES periode (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO stockages_mesures_home (id, periode_id, date_mesure, valeur_use, valeur_max) SELECT id, periode_id, date_mesure, valeur_use, valeur_max FROM __temp__stockages_mesures_home');
        $this->addSql('DROP TABLE __temp__stockages_mesures_home');
        $this->addSql('CREATE INDEX IDX_5800368AF384C1CF ON stockages_mesures_home (periode_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__stockages_mesures_work AS SELECT id, res_stockage_work_id, periode_id, date_mesure, valeur_use, valeur_max FROM stockages_mesures_work');
        $this->addSql('DROP TABLE stockages_mesures_work');
        $this->addSql('CREATE TABLE stockages_mesures_work (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, res_stockage_work_id INTEGER DEFAULT NULL, periode_id INTEGER DEFAULT NULL, date_mesure DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , valeur_use INTEGER NOT NULL, valeur_max INTEGER NOT NULL, CONSTRAINT FK_7A9852DAA4512571 FOREIGN KEY (res_stockage_work_id) REFERENCES res_stockage_work (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7A9852DAF384C1CF FOREIGN KEY (periode_id) REFERENCES periode (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO stockages_mesures_work (id, res_stockage_work_id, periode_id, date_mesure, valeur_use, valeur_max) SELECT id, res_stockage_work_id, periode_id, date_mesure, valeur_use, valeur_max FROM __temp__stockages_mesures_work');
        $this->addSql('DROP TABLE __temp__stockages_mesures_work');
        $this->addSql('CREATE INDEX IDX_7A9852DAF384C1CF ON stockages_mesures_work (periode_id)');
        $this->addSql('CREATE INDEX IDX_7A9852DAA4512571 ON stockages_mesures_work (res_stockage_work_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__stockages_mesures_home AS SELECT id, periode_id, date_mesure, valeur_use, valeur_max FROM stockages_mesures_home');
        $this->addSql('DROP TABLE stockages_mesures_home');
        $this->addSql('CREATE TABLE stockages_mesures_home (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, periode_id INTEGER DEFAULT NULL, date_mesure DATE NOT NULL, valeur_use INTEGER NOT NULL, valeur_max INTEGER NOT NULL, CONSTRAINT FK_5800368AF384C1CF FOREIGN KEY (periode_id) REFERENCES periode (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO stockages_mesures_home (id, periode_id, date_mesure, valeur_use, valeur_max) SELECT id, periode_id, date_mesure, valeur_use, valeur_max FROM __temp__stockages_mesures_home');
        $this->addSql('DROP TABLE __temp__stockages_mesures_home');
        $this->addSql('CREATE INDEX IDX_5800368AF384C1CF ON stockages_mesures_home (periode_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__stockages_mesures_work AS SELECT id, res_stockage_work_id, periode_id, date_mesure, valeur_use, valeur_max FROM stockages_mesures_work');
        $this->addSql('DROP TABLE stockages_mesures_work');
        $this->addSql('CREATE TABLE stockages_mesures_work (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, res_stockage_work_id INTEGER DEFAULT NULL, periode_id INTEGER DEFAULT NULL, date_mesure DATE NOT NULL, valeur_use INTEGER NOT NULL, valeur_max INTEGER NOT NULL, CONSTRAINT FK_7A9852DAA4512571 FOREIGN KEY (res_stockage_work_id) REFERENCES res_stockage_work (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7A9852DAF384C1CF FOREIGN KEY (periode_id) REFERENCES periode (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO stockages_mesures_work (id, res_stockage_work_id, periode_id, date_mesure, valeur_use, valeur_max) SELECT id, res_stockage_work_id, periode_id, date_mesure, valeur_use, valeur_max FROM __temp__stockages_mesures_work');
        $this->addSql('DROP TABLE __temp__stockages_mesures_work');
        $this->addSql('CREATE INDEX IDX_7A9852DAA4512571 ON stockages_mesures_work (res_stockage_work_id)');
        $this->addSql('CREATE INDEX IDX_7A9852DAF384C1CF ON stockages_mesures_work (periode_id)');
    }
}
