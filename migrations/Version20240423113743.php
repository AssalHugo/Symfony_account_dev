<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240423113743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__employe AS SELECT id, groupe_principal_id, referent_id, nom, prenom, photo, updated_at, sync_reseda, page_pro, idhal, orcid, mail_secondaire, telephone_secondaire, annee_naissance, redirection_mail FROM employe');
        $this->addSql('DROP TABLE employe');
        $this->addSql('CREATE TABLE employe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, groupe_principal_id INTEGER DEFAULT NULL, referent_id INTEGER DEFAULT NULL, nom VARCHAR(64) NOT NULL, prenom VARCHAR(64) NOT NULL, photo VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , sync_reseda BOOLEAN NOT NULL, page_pro VARCHAR(255) DEFAULT NULL, idhal VARCHAR(50) DEFAULT NULL, orcid VARCHAR(50) DEFAULT NULL, mail_secondaire VARCHAR(128) DEFAULT NULL, telephone_secondaire VARCHAR(10) DEFAULT NULL, annee_naissance INTEGER DEFAULT NULL, redirection_mail BOOLEAN NOT NULL, CONSTRAINT FK_F804D3B93B1B422A FOREIGN KEY (groupe_principal_id) REFERENCES groupes (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F804D3B935E47E35 FOREIGN KEY (referent_id) REFERENCES employe (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO employe (id, groupe_principal_id, referent_id, nom, prenom, photo, updated_at, sync_reseda, page_pro, idhal, orcid, mail_secondaire, telephone_secondaire, annee_naissance, redirection_mail) SELECT id, groupe_principal_id, referent_id, nom, prenom, photo, updated_at, sync_reseda, page_pro, idhal, orcid, mail_secondaire, telephone_secondaire, annee_naissance, redirection_mail FROM __temp__employe');
        $this->addSql('DROP TABLE __temp__employe');
        $this->addSql('CREATE INDEX IDX_F804D3B935E47E35 ON employe (referent_id)');
        $this->addSql('CREATE INDEX IDX_F804D3B93B1B422A ON employe (groupe_principal_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__serveurs_mesures AS SELECT id, res_serveur_id, date_mesure, cpu, ram_utilise, ram_max, nb_utilisateurs, cpu_total FROM serveurs_mesures');
        $this->addSql('DROP TABLE serveurs_mesures');
        $this->addSql('CREATE TABLE serveurs_mesures (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, res_serveur_id INTEGER DEFAULT NULL, date_mesure DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , cpu INTEGER DEFAULT NULL, ram_utilise INTEGER DEFAULT NULL, ram_max INTEGER DEFAULT NULL, nb_utilisateurs INTEGER DEFAULT NULL, cpu_total INTEGER DEFAULT NULL, CONSTRAINT FK_2494B7362A8E116B FOREIGN KEY (res_serveur_id) REFERENCES res_serveur (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO serveurs_mesures (id, res_serveur_id, date_mesure, cpu, ram_utilise, ram_max, nb_utilisateurs, cpu_total) SELECT id, res_serveur_id, date_mesure, cpu, ram_utilise, ram_max, nb_utilisateurs, cpu_total FROM __temp__serveurs_mesures');
        $this->addSql('DROP TABLE __temp__serveurs_mesures');
        $this->addSql('CREATE INDEX IDX_2494B7362A8E116B ON serveurs_mesures (res_serveur_id)');
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
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, employe_id, username, roles, password, email FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, employe_id INTEGER DEFAULT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, email VARCHAR(128) DEFAULT NULL, CONSTRAINT FK_8D93D6491B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user (id, employe_id, username, roles, password, email) SELECT id, employe_id, username, roles, password, email FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON user (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6491B65292 ON user (employe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__employe AS SELECT id, groupe_principal_id, referent_id, nom, prenom, photo, updated_at, sync_reseda, page_pro, idhal, orcid, mail_secondaire, telephone_secondaire, annee_naissance, redirection_mail FROM employe');
        $this->addSql('DROP TABLE employe');
        $this->addSql('CREATE TABLE employe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, groupe_principal_id INTEGER DEFAULT NULL, referent_id INTEGER DEFAULT NULL, nom VARCHAR(64) NOT NULL, prenom VARCHAR(64) NOT NULL, photo VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , sync_reseda BOOLEAN NOT NULL, page_pro VARCHAR(255) DEFAULT NULL, idhal VARCHAR(50) DEFAULT NULL, orcid VARCHAR(50) DEFAULT NULL, mail_secondaire VARCHAR(128) DEFAULT NULL, telephone_secondaire VARCHAR(10) DEFAULT NULL, annee_naissance INTEGER DEFAULT NULL, redirection_mail BOOLEAN NOT NULL, CONSTRAINT FK_F804D3B93B1B422A FOREIGN KEY (groupe_principal_id) REFERENCES groupes (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F804D3B935E47E35 FOREIGN KEY (referent_id) REFERENCES employe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO employe (id, groupe_principal_id, referent_id, nom, prenom, photo, updated_at, sync_reseda, page_pro, idhal, orcid, mail_secondaire, telephone_secondaire, annee_naissance, redirection_mail) SELECT id, groupe_principal_id, referent_id, nom, prenom, photo, updated_at, sync_reseda, page_pro, idhal, orcid, mail_secondaire, telephone_secondaire, annee_naissance, redirection_mail FROM __temp__employe');
        $this->addSql('DROP TABLE __temp__employe');
        $this->addSql('CREATE INDEX IDX_F804D3B93B1B422A ON employe (groupe_principal_id)');
        $this->addSql('CREATE INDEX IDX_F804D3B935E47E35 ON employe (referent_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__serveurs_mesures AS SELECT id, res_serveur_id, date_mesure, cpu, cpu_total, ram_utilise, ram_max, nb_utilisateurs FROM serveurs_mesures');
        $this->addSql('DROP TABLE serveurs_mesures');
        $this->addSql('CREATE TABLE serveurs_mesures (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, res_serveur_id INTEGER DEFAULT NULL, date_mesure DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , cpu INTEGER DEFAULT NULL, cpu_total INTEGER DEFAULT NULL, ram_utilise INTEGER DEFAULT NULL, ram_max INTEGER DEFAULT NULL, nb_utilisateurs INTEGER DEFAULT NULL, CONSTRAINT FK_2494B7362A8E116B FOREIGN KEY (res_serveur_id) REFERENCES res_serveur (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO serveurs_mesures (id, res_serveur_id, date_mesure, cpu, cpu_total, ram_utilise, ram_max, nb_utilisateurs) SELECT id, res_serveur_id, date_mesure, cpu, cpu_total, ram_utilise, ram_max, nb_utilisateurs FROM __temp__serveurs_mesures');
        $this->addSql('DROP TABLE __temp__serveurs_mesures');
        $this->addSql('CREATE INDEX IDX_2494B7362A8E116B ON serveurs_mesures (res_serveur_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__stockages_mesures_home AS SELECT id, periode_id, date_mesure, valeur_use, valeur_max FROM stockages_mesures_home');
        $this->addSql('DROP TABLE stockages_mesures_home');
        $this->addSql('CREATE TABLE stockages_mesures_home (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, periode_id INTEGER DEFAULT NULL, date_mesure DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , valeur_use INTEGER NOT NULL, valeur_max INTEGER NOT NULL, CONSTRAINT FK_5800368AF384C1CF FOREIGN KEY (periode_id) REFERENCES periode (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO stockages_mesures_home (id, periode_id, date_mesure, valeur_use, valeur_max) SELECT id, periode_id, date_mesure, valeur_use, valeur_max FROM __temp__stockages_mesures_home');
        $this->addSql('DROP TABLE __temp__stockages_mesures_home');
        $this->addSql('CREATE INDEX IDX_5800368AF384C1CF ON stockages_mesures_home (periode_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__stockages_mesures_work AS SELECT id, res_stockage_work_id, periode_id, date_mesure, valeur_use, valeur_max FROM stockages_mesures_work');
        $this->addSql('DROP TABLE stockages_mesures_work');
        $this->addSql('CREATE TABLE stockages_mesures_work (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, res_stockage_work_id INTEGER DEFAULT NULL, periode_id INTEGER DEFAULT NULL, date_mesure DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , valeur_use INTEGER NOT NULL, valeur_max INTEGER NOT NULL, CONSTRAINT FK_7A9852DAA4512571 FOREIGN KEY (res_stockage_work_id) REFERENCES res_stockage_work (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7A9852DAF384C1CF FOREIGN KEY (periode_id) REFERENCES periode (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO stockages_mesures_work (id, res_stockage_work_id, periode_id, date_mesure, valeur_use, valeur_max) SELECT id, res_stockage_work_id, periode_id, date_mesure, valeur_use, valeur_max FROM __temp__stockages_mesures_work');
        $this->addSql('DROP TABLE __temp__stockages_mesures_work');
        $this->addSql('CREATE INDEX IDX_7A9852DAA4512571 ON stockages_mesures_work (res_stockage_work_id)');
        $this->addSql('CREATE INDEX IDX_7A9852DAF384C1CF ON stockages_mesures_work (periode_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, employe_id, username, roles, password, email FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, employe_id INTEGER DEFAULT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, email VARCHAR(128) NOT NULL, CONSTRAINT FK_8D93D6491B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user (id, employe_id, username, roles, password, email) SELECT id, employe_id, username, roles, password, email FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6491B65292 ON user (employe_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON user (username)');
    }
}
