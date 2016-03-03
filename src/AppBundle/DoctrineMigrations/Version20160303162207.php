<?php

namespace Venice\AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160303162207 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE trinity_settings CHANGE group_name group_name VARCHAR(64) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX unique_name ON trinity_settings (name)');
        $this->addSql('CREATE UNIQUE INDEX unique_name_group ON trinity_settings (name, group_name)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP INDEX unique_name ON trinity_settings');
        $this->addSql('DROP INDEX unique_name_group ON trinity_settings');
        $this->addSql('ALTER TABLE trinity_settings CHANGE group_name group_name LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
