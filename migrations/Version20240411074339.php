<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411074339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE serveurs_mesures ADD COLUMN cpu_total INTEGER NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__serveurs_mesures AS SELECT id, res_serveur_id, date_mesure, cpu, ram_utilise, ram_max, nb_utilisateurs FROM serveurs_mesures');
        $this->addSql('DROP TABLE serveurs_mesures');
        $this->addSql('CREATE TABLE serveurs_mesures (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, res_serveur_id INTEGER DEFAULT NULL, date_mesure DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , cpu INTEGER NOT NULL, ram_utilise INTEGER NOT NULL, ram_max INTEGER NOT NULL, nb_utilisateurs INTEGER NOT NULL, CONSTRAINT FK_2494B7362A8E116B FOREIGN KEY (res_serveur_id) REFERENCES res_serveur (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO serveurs_mesures (id, res_serveur_id, date_mesure, cpu, ram_utilise, ram_max, nb_utilisateurs) SELECT id, res_serveur_id, date_mesure, cpu, ram_utilise, ram_max, nb_utilisateurs FROM __temp__serveurs_mesures');
        $this->addSql('DROP TABLE __temp__serveurs_mesures');
        $this->addSql('CREATE INDEX IDX_2494B7362A8E116B ON serveurs_mesures (res_serveur_id)');
    }
}
