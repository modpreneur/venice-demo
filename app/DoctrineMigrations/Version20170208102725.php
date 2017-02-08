<?php

namespace VeniceApp\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170208102725 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE content ADD http_stream VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE product_type product_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD flomersion_start DATE DEFAULT NULL, ADD flomersion_end DATE DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE content DROP http_stream');
        $this->addSql('ALTER TABLE product CHANGE product_type product_type VARCHAR(255) DEFAULT \'digital\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE user DROP flomersion_start, DROP flomersion_end');
    }
}
