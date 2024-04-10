<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240410140837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE batiments (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(40) NOT NULL)');
        $this->addSql('CREATE TABLE contrats (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, status_id INTEGER DEFAULT NULL, employe_id INTEGER DEFAULT NULL, date_debut DATE NOT NULL, date_fin DATE DEFAULT NULL, remarque VARCHAR(128) DEFAULT NULL, quotite INTEGER DEFAULT NULL, CONSTRAINT FK_7268396C6BF700BD FOREIGN KEY (status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7268396C1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_7268396C6BF700BD ON contrats (status_id)');
        $this->addSql('CREATE INDEX IDX_7268396C1B65292 ON contrats (employe_id)');
        $this->addSql('CREATE TABLE departement (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, responsable_id INTEGER NOT NULL, nom VARCHAR(40) NOT NULL, acronyme VARCHAR(8) NOT NULL, CONSTRAINT FK_C1765B6353C59D72 FOREIGN KEY (responsable_id) REFERENCES employe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C1765B6353C59D72 ON departement (responsable_id)');
        $this->addSql('CREATE TABLE employe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, groupe_principal_id INTEGER DEFAULT NULL, referent_id INTEGER DEFAULT NULL, nom VARCHAR(64) NOT NULL, prenom VARCHAR(64) NOT NULL, photo VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , sync_reseda BOOLEAN NOT NULL, page_pro VARCHAR(255) DEFAULT NULL, idhal VARCHAR(50) DEFAULT NULL, orcid VARCHAR(50) DEFAULT NULL, mail_secondaire VARCHAR(128) DEFAULT NULL, telephone_secondaire VARCHAR(10) DEFAULT NULL, annee_naissance INTEGER DEFAULT NULL, redirection_mail BOOLEAN NOT NULL, CONSTRAINT FK_F804D3B93B1B422A FOREIGN KEY (groupe_principal_id) REFERENCES groupes (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F804D3B935E47E35 FOREIGN KEY (referent_id) REFERENCES employe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F804D3B93B1B422A ON employe (groupe_principal_id)');
        $this->addSql('CREATE INDEX IDX_F804D3B935E47E35 ON employe (referent_id)');
        $this->addSql('CREATE TABLE employe_localisations (employe_id INTEGER NOT NULL, localisations_id INTEGER NOT NULL, PRIMARY KEY(employe_id, localisations_id), CONSTRAINT FK_6B3955B21B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6B3955B21E0EE9AA FOREIGN KEY (localisations_id) REFERENCES localisations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6B3955B21B65292 ON employe_localisations (employe_id)');
        $this->addSql('CREATE INDEX IDX_6B3955B21E0EE9AA ON employe_localisations (localisations_id)');
        $this->addSql('CREATE TABLE employe_groupes (employe_id INTEGER NOT NULL, groupes_id INTEGER NOT NULL, PRIMARY KEY(employe_id, groupes_id), CONSTRAINT FK_936C0A7A1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_936C0A7A305371B FOREIGN KEY (groupes_id) REFERENCES groupes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_936C0A7A1B65292 ON employe_groupes (employe_id)');
        $this->addSql('CREATE INDEX IDX_936C0A7A305371B ON employe_groupes (groupes_id)');
        $this->addSql('CREATE TABLE etats_requetes (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, etat VARCHAR(30) NOT NULL)');
        $this->addSql('CREATE TABLE groupes (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, responsable_id INTEGER DEFAULT NULL, departement_id INTEGER DEFAULT NULL, res_stockage_work_id INTEGER DEFAULT NULL, nom VARCHAR(32) NOT NULL, acronyme VARCHAR(8) DEFAULT NULL, CONSTRAINT FK_576366D953C59D72 FOREIGN KEY (responsable_id) REFERENCES employe (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_576366D9CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_576366D9A4512571 FOREIGN KEY (res_stockage_work_id) REFERENCES res_stockage_work (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_576366D953C59D72 ON groupes (responsable_id)');
        $this->addSql('CREATE INDEX IDX_576366D9CCF9E01E ON groupes (departement_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_576366D9A4512571 ON groupes (res_stockage_work_id)');
        $this->addSql('CREATE TABLE groupes_employe (groupes_id INTEGER NOT NULL, employe_id INTEGER NOT NULL, PRIMARY KEY(groupes_id, employe_id), CONSTRAINT FK_792B4803305371B FOREIGN KEY (groupes_id) REFERENCES groupes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_792B48031B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_792B4803305371B ON groupes_employe (groupes_id)');
        $this->addSql('CREATE INDEX IDX_792B48031B65292 ON groupes_employe (employe_id)');
        $this->addSql('CREATE TABLE localisations (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, batiment_id INTEGER NOT NULL, bureau VARCHAR(64) NOT NULL, CONSTRAINT FK_66B68274D6F6891B FOREIGN KEY (batiment_id) REFERENCES batiments (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_66B68274D6F6891B ON localisations (batiment_id)');
        $this->addSql('CREATE TABLE periode (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(5) NOT NULL)');
        $this->addSql('CREATE TABLE requetes (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, groupe_principal_id INTEGER DEFAULT NULL, localisation_id INTEGER DEFAULT NULL, referent_id INTEGER DEFAULT NULL, etat_requete_id INTEGER DEFAULT NULL, contrat_id INTEGER NOT NULL, nom VARCHAR(128) NOT NULL, prenom VARCHAR(128) NOT NULL, mail VARCHAR(128) NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, telephone VARCHAR(10) DEFAULT NULL, date_requete DATETIME DEFAULT NULL, date_validation DATETIME DEFAULT NULL, CONSTRAINT FK_2D13E3C43B1B422A FOREIGN KEY (groupe_principal_id) REFERENCES groupes (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C4C68BE09C FOREIGN KEY (localisation_id) REFERENCES localisations (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C435E47E35 FOREIGN KEY (referent_id) REFERENCES employe (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C4B1B7685A FOREIGN KEY (etat_requete_id) REFERENCES etats_requetes (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C41823061F FOREIGN KEY (contrat_id) REFERENCES contrats (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2D13E3C43B1B422A ON requetes (groupe_principal_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2D13E3C4C68BE09C ON requetes (localisation_id)');
        $this->addSql('CREATE INDEX IDX_2D13E3C435E47E35 ON requetes (referent_id)');
        $this->addSql('CREATE INDEX IDX_2D13E3C4B1B7685A ON requetes (etat_requete_id)');
        $this->addSql('CREATE INDEX IDX_2D13E3C41823061F ON requetes (contrat_id)');
        $this->addSql('CREATE TABLE res_stockage_work (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(40) NOT NULL, path VARCHAR(64) DEFAULT NULL)');
        $this->addSql('CREATE TABLE res_stockage_work_employe (res_stockage_work_id INTEGER NOT NULL, employe_id INTEGER NOT NULL, PRIMARY KEY(res_stockage_work_id, employe_id), CONSTRAINT FK_DA4A14B9A4512571 FOREIGN KEY (res_stockage_work_id) REFERENCES res_stockage_work (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_DA4A14B91B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_DA4A14B9A4512571 ON res_stockage_work_employe (res_stockage_work_id)');
        $this->addSql('CREATE INDEX IDX_DA4A14B91B65292 ON res_stockage_work_employe (employe_id)');
        $this->addSql('CREATE TABLE res_stockages_home (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, nom VARCHAR(40) NOT NULL, path VARCHAR(128) NOT NULL, CONSTRAINT FK_79387CB5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_79387CB5A76ED395 ON res_stockages_home (user_id)');
        $this->addSql('CREATE TABLE res_stockages_home_stockages_mesures_home (res_stockages_home_id INTEGER NOT NULL, stockages_mesures_home_id INTEGER NOT NULL, PRIMARY KEY(res_stockages_home_id, stockages_mesures_home_id), CONSTRAINT FK_A379D2E2E5C77D6 FOREIGN KEY (res_stockages_home_id) REFERENCES res_stockages_home (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A379D2E23FFCA4B FOREIGN KEY (stockages_mesures_home_id) REFERENCES stockages_mesures_home (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A379D2E2E5C77D6 ON res_stockages_home_stockages_mesures_home (res_stockages_home_id)');
        $this->addSql('CREATE INDEX IDX_A379D2E23FFCA4B ON res_stockages_home_stockages_mesures_home (stockages_mesures_home_id)');
        $this->addSql('CREATE TABLE status (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(32) NOT NULL)');
        $this->addSql('CREATE TABLE stockages_mesures_home (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, periode_id INTEGER DEFAULT NULL, date_mesure DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , valeur_use INTEGER NOT NULL, valeur_max INTEGER NOT NULL, CONSTRAINT FK_5800368AF384C1CF FOREIGN KEY (periode_id) REFERENCES periode (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5800368AF384C1CF ON stockages_mesures_home (periode_id)');
        $this->addSql('CREATE TABLE stockages_mesures_work (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, res_stockage_work_id INTEGER DEFAULT NULL, periode_id INTEGER DEFAULT NULL, date_mesure DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , valeur_use INTEGER NOT NULL, valeur_max INTEGER NOT NULL, CONSTRAINT FK_7A9852DAA4512571 FOREIGN KEY (res_stockage_work_id) REFERENCES res_stockage_work (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7A9852DAF384C1CF FOREIGN KEY (periode_id) REFERENCES periode (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_7A9852DAA4512571 ON stockages_mesures_work (res_stockage_work_id)');
        $this->addSql('CREATE INDEX IDX_7A9852DAF384C1CF ON stockages_mesures_work (periode_id)');
        $this->addSql('CREATE TABLE telephones (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, employe_id INTEGER DEFAULT NULL, numero VARCHAR(10) NOT NULL, CONSTRAINT FK_6FCD09F1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6FCD09F1B65292 ON telephones (employe_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, employe_id INTEGER DEFAULT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, email VARCHAR(128) NOT NULL, CONSTRAINT FK_8D93D6491B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6491B65292 ON user (employe_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE batiments');
        $this->addSql('DROP TABLE contrats');
        $this->addSql('DROP TABLE departement');
        $this->addSql('DROP TABLE employe');
        $this->addSql('DROP TABLE employe_localisations');
        $this->addSql('DROP TABLE employe_groupes');
        $this->addSql('DROP TABLE etats_requetes');
        $this->addSql('DROP TABLE groupes');
        $this->addSql('DROP TABLE groupes_employe');
        $this->addSql('DROP TABLE localisations');
        $this->addSql('DROP TABLE periode');
        $this->addSql('DROP TABLE requetes');
        $this->addSql('DROP TABLE res_stockage_work');
        $this->addSql('DROP TABLE res_stockage_work_employe');
        $this->addSql('DROP TABLE res_stockages_home');
        $this->addSql('DROP TABLE res_stockages_home_stockages_mesures_home');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE stockages_mesures_home');
        $this->addSql('DROP TABLE stockages_mesures_work');
        $this->addSql('DROP TABLE telephones');
        $this->addSql('DROP TABLE user');
    }
}
