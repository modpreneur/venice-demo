<?php

namespace VeniceApp\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170202084727 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE profile_photo (id INT AUTO_INCREMENT NOT NULL, image_name VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, crop_start_x INT DEFAULT 0 NOT NULL, crop_start_y INT DEFAULT 0 NOT NULL, crop_size INT DEFAULT 100 NOT NULL, original_photo_url LONGTEXT DEFAULT NULL, crooped_photo_url LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD profile_photo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64987F42D3D FOREIGN KEY (profile_photo_id) REFERENCES profile_photo (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64987F42D3D ON user (profile_photo_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64987F42D3D');
        $this->addSql('DROP TABLE profile_photo');
        $this->addSql('DROP INDEX IDX_8D93D64987F42D3D ON user');
        $this->addSql('ALTER TABLE user DROP profile_photo_id');
    }
}
