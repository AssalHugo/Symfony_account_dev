<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240318143236 extends AbstractMigration
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
        $this->addSql('CREATE TABLE employe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, groupe_principal_id INTEGER DEFAULT NULL, nom VARCHAR(64) NOT NULL, prenom VARCHAR(64) NOT NULL, photo VARCHAR(255) DEFAULT NULL, sync_reseda BOOLEAN NOT NULL, page_pro VARCHAR(255) DEFAULT NULL, idhal VARCHAR(50) DEFAULT NULL, orcid VARCHAR(50) DEFAULT NULL, mail_secondaire VARCHAR(128) DEFAULT NULL, telephone_secondaire VARCHAR(10) DEFAULT NULL, annee_naissance INTEGER DEFAULT NULL, CONSTRAINT FK_F804D3B93B1B422A FOREIGN KEY (groupe_principal_id) REFERENCES groupes (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F804D3B93B1B422A ON employe (groupe_principal_id)');
        $this->addSql('CREATE TABLE employe_localisations (employe_id INTEGER NOT NULL, localisations_id INTEGER NOT NULL, PRIMARY KEY(employe_id, localisations_id), CONSTRAINT FK_6B3955B21B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6B3955B21E0EE9AA FOREIGN KEY (localisations_id) REFERENCES localisations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6B3955B21B65292 ON employe_localisations (employe_id)');
        $this->addSql('CREATE INDEX IDX_6B3955B21E0EE9AA ON employe_localisations (localisations_id)');
        $this->addSql('CREATE TABLE employe_groupes (employe_id INTEGER NOT NULL, groupes_id INTEGER NOT NULL, PRIMARY KEY(employe_id, groupes_id), CONSTRAINT FK_936C0A7A1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_936C0A7A305371B FOREIGN KEY (groupes_id) REFERENCES groupes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_936C0A7A1B65292 ON employe_groupes (employe_id)');
        $this->addSql('CREATE INDEX IDX_936C0A7A305371B ON employe_groupes (groupes_id)');
        $this->addSql('CREATE TABLE groupes (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, responsable_id INTEGER NOT NULL, nom VARCHAR(32) NOT NULL, acronyme VARCHAR(8) DEFAULT NULL, statut VARCHAR(34) NOT NULL, CONSTRAINT FK_576366D953C59D72 FOREIGN KEY (responsable_id) REFERENCES employe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_576366D953C59D72 ON groupes (responsable_id)');
        $this->addSql('CREATE TABLE groupes_employe (groupes_id INTEGER NOT NULL, employe_id INTEGER NOT NULL, PRIMARY KEY(groupes_id, employe_id), CONSTRAINT FK_792B4803305371B FOREIGN KEY (groupes_id) REFERENCES groupes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_792B48031B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_792B4803305371B ON groupes_employe (groupes_id)');
        $this->addSql('CREATE INDEX IDX_792B48031B65292 ON groupes_employe (employe_id)');
        $this->addSql('CREATE TABLE localisations (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, batiment_id INTEGER NOT NULL, bureau VARCHAR(64) NOT NULL, CONSTRAINT FK_66B68274D6F6891B FOREIGN KEY (batiment_id) REFERENCES batiments (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_66B68274D6F6891B ON localisations (batiment_id)');
        $this->addSql('CREATE TABLE status (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(32) NOT NULL)');
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
        $this->addSql('DROP TABLE employe');
        $this->addSql('DROP TABLE employe_localisations');
        $this->addSql('DROP TABLE employe_groupes');
        $this->addSql('DROP TABLE groupes');
        $this->addSql('DROP TABLE groupes_employe');
        $this->addSql('DROP TABLE localisations');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE telephones');
        $this->addSql('DROP TABLE user');
    }
}
