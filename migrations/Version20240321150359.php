<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240321150359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__requetes AS SELECT id, groupe_principal_id, localisation_id, referent_id, etat_requete_id, nom, prenom, mail, commentaire FROM requetes');
        $this->addSql('DROP TABLE requetes');
        $this->addSql('CREATE TABLE requetes (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, groupe_principal_id INTEGER DEFAULT NULL, localisation_id INTEGER DEFAULT NULL, referent_id INTEGER DEFAULT NULL, etat_requete_id INTEGER DEFAULT NULL, contrat_id INTEGER NOT NULL, nom VARCHAR(128) NOT NULL, prenom VARCHAR(128) NOT NULL, mail VARCHAR(128) NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_2D13E3C43B1B422A FOREIGN KEY (groupe_principal_id) REFERENCES groupes (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C4C68BE09C FOREIGN KEY (localisation_id) REFERENCES localisations (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C435E47E35 FOREIGN KEY (referent_id) REFERENCES employe (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C4B1B7685A FOREIGN KEY (etat_requete_id) REFERENCES etats_requetes (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C41823061F FOREIGN KEY (contrat_id) REFERENCES contrats (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO requetes (id, groupe_principal_id, localisation_id, referent_id, etat_requete_id, nom, prenom, mail, commentaire) SELECT id, groupe_principal_id, localisation_id, referent_id, etat_requete_id, nom, prenom, mail, commentaire FROM __temp__requetes');
        $this->addSql('DROP TABLE __temp__requetes');
        $this->addSql('CREATE INDEX IDX_2D13E3C4B1B7685A ON requetes (etat_requete_id)');
        $this->addSql('CREATE INDEX IDX_2D13E3C43B1B422A ON requetes (groupe_principal_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2D13E3C4C68BE09C ON requetes (localisation_id)');
        $this->addSql('CREATE INDEX IDX_2D13E3C435E47E35 ON requetes (referent_id)');
        $this->addSql('CREATE INDEX IDX_2D13E3C41823061F ON requetes (contrat_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__requetes AS SELECT id, groupe_principal_id, localisation_id, referent_id, etat_requete_id, nom, prenom, mail, commentaire FROM requetes');
        $this->addSql('DROP TABLE requetes');
        $this->addSql('CREATE TABLE requetes (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, groupe_principal_id INTEGER DEFAULT NULL, localisation_id INTEGER DEFAULT NULL, referent_id INTEGER DEFAULT NULL, etat_requete_id INTEGER DEFAULT NULL, nom VARCHAR(128) NOT NULL, prenom VARCHAR(128) NOT NULL, mail VARCHAR(128) NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_2D13E3C43B1B422A FOREIGN KEY (groupe_principal_id) REFERENCES groupes (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C4C68BE09C FOREIGN KEY (localisation_id) REFERENCES localisations (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C435E47E35 FOREIGN KEY (referent_id) REFERENCES employe (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2D13E3C4B1B7685A FOREIGN KEY (etat_requete_id) REFERENCES etats_requetes (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO requetes (id, groupe_principal_id, localisation_id, referent_id, etat_requete_id, nom, prenom, mail, commentaire) SELECT id, groupe_principal_id, localisation_id, referent_id, etat_requete_id, nom, prenom, mail, commentaire FROM __temp__requetes');
        $this->addSql('DROP TABLE __temp__requetes');
        $this->addSql('CREATE INDEX IDX_2D13E3C43B1B422A ON requetes (groupe_principal_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2D13E3C4C68BE09C ON requetes (localisation_id)');
        $this->addSql('CREATE INDEX IDX_2D13E3C435E47E35 ON requetes (referent_id)');
        $this->addSql('CREATE INDEX IDX_2D13E3C4B1B7685A ON requetes (etat_requete_id)');
    }
}
