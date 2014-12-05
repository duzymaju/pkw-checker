<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141127145530 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE community (id INT NOT NULL, district_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_1B604033B08FA272 (district_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE constituency (id INT AUTO_INCREMENT NOT NULL, community_id INT DEFAULT NULL, number INT NOT NULL, INDEX IDX_5F387769FDA7B0BF (community_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE district (id INT NOT NULL, province_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_31C15487E946114A (province_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE polling_station (id INT AUTO_INCREMENT NOT NULL, constituency_id INT DEFAULT NULL, number INT NOT NULL, `key` CHAR(32) NOT NULL, INDEX IDX_E031160E693B626F (constituency_id), UNIQUE INDEX L_UNIQUE_IDX_1 (`key`), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE province (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE community ADD CONSTRAINT FK_1B604033B08FA272 FOREIGN KEY (district_id) REFERENCES district (id)');
        $this->addSql('ALTER TABLE constituency ADD CONSTRAINT FK_5F387769FDA7B0BF FOREIGN KEY (community_id) REFERENCES community (id)');
        $this->addSql('ALTER TABLE district ADD CONSTRAINT FK_31C15487E946114A FOREIGN KEY (province_id) REFERENCES province (id)');
        $this->addSql('ALTER TABLE polling_station ADD CONSTRAINT FK_E031160E693B626F FOREIGN KEY (constituency_id) REFERENCES constituency (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE constituency DROP FOREIGN KEY FK_5F387769FDA7B0BF');
        $this->addSql('ALTER TABLE polling_station DROP FOREIGN KEY FK_E031160E693B626F');
        $this->addSql('ALTER TABLE community DROP FOREIGN KEY FK_1B604033B08FA272');
        $this->addSql('ALTER TABLE district DROP FOREIGN KEY FK_31C15487E946114A');
        $this->addSql('DROP TABLE community');
        $this->addSql('DROP TABLE constituency');
        $this->addSql('DROP TABLE district');
        $this->addSql('DROP TABLE polling_station');
        $this->addSql('DROP TABLE province');
    }
}
