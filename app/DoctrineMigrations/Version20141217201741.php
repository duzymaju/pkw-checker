<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141217201741 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE constituency DROP FOREIGN KEY FK_5F387769FDA7B0BF');
        $this->addSql('DROP INDEX IDX_5F387769FDA7B0BF ON constituency');
        $this->addSql('ALTER TABLE constituency DROP community_id');
        $this->addSql('ALTER TABLE district ADD constituency_id INT DEFAULT NULL AFTER province_id');
        $this->addSql('ALTER TABLE district ADD CONSTRAINT FK_31C15487693B626F FOREIGN KEY (constituency_id) REFERENCES constituency (id)');
        $this->addSql('CREATE INDEX IDX_31C15487693B626F ON district (constituency_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE constituency ADD community_id INT DEFAULT NULL AFTER id');
        $this->addSql('ALTER TABLE constituency ADD CONSTRAINT FK_5F387769FDA7B0BF FOREIGN KEY (community_id) REFERENCES community (id)');
        $this->addSql('CREATE INDEX IDX_5F387769FDA7B0BF ON constituency (community_id)');
        $this->addSql('ALTER TABLE district DROP FOREIGN KEY FK_31C15487693B626F');
        $this->addSql('DROP INDEX IDX_31C15487693B626F ON district');
        $this->addSql('ALTER TABLE district DROP constituency_id');
    }
}
