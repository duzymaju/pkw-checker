<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141218112806 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('ALTER TABLE constituency ADD candidates_number INT NOT NULL, ADD mandates_number INT NOT NULL, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE polling_station CHANGE id id INT NOT NULL');
        $this->addSql('CREATE TABLE committee (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('ALTER TABLE constituency DROP candidates_number, DROP mandates_number, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE polling_station CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('DROP TABLE committee');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }
}
