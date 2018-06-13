<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170606000305 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE device (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, uuid VARCHAR(255) NOT NULL, os VARCHAR(150) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(45) DEFAULT NULL, updated_from_ip VARCHAR(45) DEFAULT NULL, UNIQUE INDEX UNIQ_92FB68ED17F50A6 (uuid), INDEX IDX_92FB68EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68EA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE device');
    }
}
