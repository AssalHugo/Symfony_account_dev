<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240404142719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE res_stockages_home_stockage_mesures_home (res_stockages_home_id INTEGER NOT NULL, stockage_mesures_home_id INTEGER NOT NULL, PRIMARY KEY(res_stockages_home_id, stockage_mesures_home_id), CONSTRAINT FK_D55B6123E5C77D6 FOREIGN KEY (res_stockages_home_id) REFERENCES res_stockages_home (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D55B6123D656D371 FOREIGN KEY (stockage_mesures_home_id) REFERENCES stockage_mesures_home (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D55B6123E5C77D6 ON res_stockages_home_stockage_mesures_home (res_stockages_home_id)');
        $this->addSql('CREATE INDEX IDX_D55B6123D656D371 ON res_stockages_home_stockage_mesures_home (stockage_mesures_home_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE res_stockages_home_stockage_mesures_home');
    }
}
