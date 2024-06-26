<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240424082640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__requetes AS SELECT id, groupe_principal_id, localisation_id, referent_id, etat_requete_id, contrat_id, nom, prenom, mail, commentaire, telephone, date_requete, date_validation FROM requetes');
        $this->addSql('DROP TABLE requetes');
        $this->addSql('CREATE TABLE requetes (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, groupe_principal_id INTEGER DEFAULT NULL, localisation_id INTEGER DEFAULT NULL, referent_id INTEGER DEFAULT NULL, etat_requete_id INTEGER DEFAULT NULL, contrat_id INTEGER NOT NULL, etat_systeme_requete_id INTEGER DEFAULT NULL, user_cree_id INTEGER DEFAULT NULL, nom VARCHAR(128) NOT NULL, prenom VARCHAR(128) NOT NULL, mail VARCHAR(128) NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, telephone VARCHAR(10) DEFAULT NULL, date_requete DATETIME DEFAULT NULL, date_validation DATETIME DEFAULT NULL, mdp_provisoire VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_2D13E3C43B1B422A FOREIGN KEY (groupe_principal_id) REFERENCES groupes (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C4C68BE09C FOREIGN KEY (localisation_id) REFERENCES localisations (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C435E47E35 FOREIGN KEY (referent_id) REFERENCES employe (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C4B1B7685A FOREIGN KEY (etat_requete_id) REFERENCES etats_requetes (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C41823061F FOREIGN KEY (contrat_id) REFERENCES contrats (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C4842BE6D3 FOREIGN KEY (etat_systeme_requete_id) REFERENCES etat_systeme_requete (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C4FDFFD8E8 FOREIGN KEY (user_cree_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO requetes (id, groupe_principal_id, localisation_id, referent_id, etat_requete_id, contrat_id, nom, prenom, mail, commentaire, telephone, date_requete, date_validation) SELECT id, groupe_principal_id, localisation_id, referent_id, etat_requete_id, contrat_id, nom, prenom, mail, commentaire, telephone, date_requete, date_validation FROM __temp__requetes');
        $this->addSql('DROP TABLE __temp__requetes');
        $this->addSql('CREATE INDEX IDX_2D13E3C41823061F ON requetes (contrat_id)');
        $this->addSql('CREATE INDEX IDX_2D13E3C4B1B7685A ON requetes (etat_requete_id)');
        $this->addSql('CREATE INDEX IDX_2D13E3C435E47E35 ON requetes (referent_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2D13E3C4C68BE09C ON requetes (localisation_id)');
        $this->addSql('CREATE INDEX IDX_2D13E3C43B1B422A ON requetes (groupe_principal_id)');
        $this->addSql('CREATE INDEX IDX_2D13E3C4842BE6D3 ON requetes (etat_systeme_requete_id)');
        $this->addSql('CREATE INDEX IDX_2D13E3C4FDFFD8E8 ON requetes (user_cree_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__requetes AS SELECT id, groupe_principal_id, localisation_id, referent_id, etat_requete_id, contrat_id, nom, prenom, mail, commentaire, telephone, date_requete, date_validation FROM requetes');
        $this->addSql('DROP TABLE requetes');
        $this->addSql('CREATE TABLE requetes (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, groupe_principal_id INTEGER DEFAULT NULL, localisation_id INTEGER DEFAULT NULL, referent_id INTEGER DEFAULT NULL, etat_requete_id INTEGER DEFAULT NULL, contrat_id INTEGER NOT NULL, nom VARCHAR(128) NOT NULL, prenom VARCHAR(128) NOT NULL, mail VARCHAR(128) NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, telephone VARCHAR(10) DEFAULT NULL, date_requete DATETIME DEFAULT NULL, date_validation DATETIME DEFAULT NULL, CONSTRAINT FK_2D13E3C43B1B422A FOREIGN KEY (groupe_principal_id) REFERENCES groupes (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C4C68BE09C FOREIGN KEY (localisation_id) REFERENCES localisations (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C435E47E35 FOREIGN KEY (referent_id) REFERENCES employe (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C4B1B7685A FOREIGN KEY (etat_requete_id) REFERENCES etats_requetes (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C41823061F FOREIGN KEY (contrat_id) REFERENCES contrats (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO requetes (id, groupe_principal_id, localisation_id, referent_id, etat_requete_id, contrat_id, nom, prenom, mail, commentaire, telephone, date_requete, date_validation) SELECT id, groupe_principal_id, localisation_id, referent_id, etat_requete_id, contrat_id, nom, prenom, mail, commentaire, telephone, date_requete, date_validation FROM __temp__requetes');
        $this->addSql('DROP TABLE __temp__requetes');
        $this->addSql('CREATE INDEX IDX_2D13E3C43B1B422A ON requetes (groupe_principal_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2D13E3C4C68BE09C ON requetes (localisation_id)');
        $this->addSql('CREATE INDEX IDX_2D13E3C435E47E35 ON requetes (referent_id)');
        $this->addSql('CREATE INDEX IDX_2D13E3C4B1B7685A ON requetes (etat_requete_id)');
        $this->addSql('CREATE INDEX IDX_2D13E3C41823061F ON requetes (contrat_id)');
    }
}
