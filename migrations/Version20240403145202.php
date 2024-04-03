<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240403145202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__employe AS SELECT id, groupe_principal_id, referent_id, nom, prenom, photo, sync_reseda, page_pro, idhal, orcid, mail_secondaire, telephone_secondaire, annee_naissance, redirection_mail, updated_at FROM employe');
        $this->addSql('DROP TABLE employe');
        $this->addSql('CREATE TABLE employe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, groupe_principal_id INTEGER DEFAULT NULL, referent_id INTEGER DEFAULT NULL, nom VARCHAR(64) NOT NULL, prenom VARCHAR(64) NOT NULL, photo VARCHAR(255) DEFAULT NULL, sync_reseda BOOLEAN NOT NULL, page_pro VARCHAR(255) DEFAULT NULL, idhal VARCHAR(50) DEFAULT NULL, orcid VARCHAR(50) DEFAULT NULL, mail_secondaire VARCHAR(128) DEFAULT NULL, telephone_secondaire VARCHAR(10) DEFAULT NULL, annee_naissance INTEGER DEFAULT NULL, redirection_mail BOOLEAN NOT NULL, updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_F804D3B93B1B422A FOREIGN KEY (groupe_principal_id) REFERENCES groupes (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F804D3B935E47E35 FOREIGN KEY (referent_id) REFERENCES employe (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO employe (id, groupe_principal_id, referent_id, nom, prenom, photo, sync_reseda, page_pro, idhal, orcid, mail_secondaire, telephone_secondaire, annee_naissance, redirection_mail, updated_at) SELECT id, groupe_principal_id, referent_id, nom, prenom, photo, sync_reseda, page_pro, idhal, orcid, mail_secondaire, telephone_secondaire, annee_naissance, redirection_mail, updated_at FROM __temp__employe');
        $this->addSql('DROP TABLE __temp__employe');
        $this->addSql('CREATE INDEX IDX_F804D3B935E47E35 ON employe (referent_id)');
        $this->addSql('CREATE INDEX IDX_F804D3B93B1B422A ON employe (groupe_principal_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__groupes AS SELECT id, departement_id, responsable_id, nom, acronyme FROM groupes');
        $this->addSql('DROP TABLE groupes');
        $this->addSql('CREATE TABLE groupes (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, departement_id INTEGER DEFAULT NULL, responsable_id INTEGER DEFAULT NULL, nom VARCHAR(32) NOT NULL, acronyme VARCHAR(8) DEFAULT NULL, CONSTRAINT FK_576366D9CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_576366D953C59D72 FOREIGN KEY (responsable_id) REFERENCES employe (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO groupes (id, departement_id, responsable_id, nom, acronyme) SELECT id, departement_id, responsable_id, nom, acronyme FROM __temp__groupes');
        $this->addSql('DROP TABLE __temp__groupes');
        $this->addSql('CREATE INDEX IDX_576366D953C59D72 ON groupes (responsable_id)');
        $this->addSql('CREATE INDEX IDX_576366D9CCF9E01E ON groupes (departement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__employe AS SELECT id, groupe_principal_id, referent_id, nom, prenom, photo, updated_at, sync_reseda, page_pro, idhal, orcid, mail_secondaire, telephone_secondaire, annee_naissance, redirection_mail FROM employe');
        $this->addSql('DROP TABLE employe');
        $this->addSql('CREATE TABLE employe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, groupe_principal_id INTEGER DEFAULT NULL, referent_id INTEGER DEFAULT NULL, nom VARCHAR(64) NOT NULL, prenom VARCHAR(64) NOT NULL, photo VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, sync_reseda BOOLEAN NOT NULL, page_pro VARCHAR(255) DEFAULT NULL, idhal VARCHAR(50) DEFAULT NULL, orcid VARCHAR(50) DEFAULT NULL, mail_secondaire VARCHAR(128) DEFAULT NULL, telephone_secondaire VARCHAR(10) DEFAULT NULL, annee_naissance INTEGER DEFAULT NULL, redirection_mail BOOLEAN NOT NULL, CONSTRAINT FK_F804D3B93B1B422A FOREIGN KEY (groupe_principal_id) REFERENCES groupes (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F804D3B935E47E35 FOREIGN KEY (referent_id) REFERENCES employe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO employe (id, groupe_principal_id, referent_id, nom, prenom, photo, updated_at, sync_reseda, page_pro, idhal, orcid, mail_secondaire, telephone_secondaire, annee_naissance, redirection_mail) SELECT id, groupe_principal_id, referent_id, nom, prenom, photo, updated_at, sync_reseda, page_pro, idhal, orcid, mail_secondaire, telephone_secondaire, annee_naissance, redirection_mail FROM __temp__employe');
        $this->addSql('DROP TABLE __temp__employe');
        $this->addSql('CREATE INDEX IDX_F804D3B93B1B422A ON employe (groupe_principal_id)');
        $this->addSql('CREATE INDEX IDX_F804D3B935E47E35 ON employe (referent_id)');
        $this->addSql('ALTER TABLE groupes ADD COLUMN statut VARCHAR(34) NOT NULL');
    }
}
