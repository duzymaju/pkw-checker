<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141208072641 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE province ADD polling_stations_number INT NOT NULL, DROP residents_number, DROP districts_number, DROP communities_number');
        $this->addSql('ALTER TABLE district ADD electorates_number INT NOT NULL, ADD polling_stations_number INT NOT NULL');
        $this->addSql('ALTER TABLE community ADD type SMALLINT NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE community DROP type');
        $this->addSql('ALTER TABLE district DROP electorates_number, DROP polling_stations_number');
        $this->addSql('ALTER TABLE province ADD districts_number INT NOT NULL, ADD communities_number INT NOT NULL, CHANGE polling_stations_number residents_number INT NOT NULL');
    }
}
