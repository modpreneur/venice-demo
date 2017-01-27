<?php

namespace VeniceApp\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170127122632 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE blog_article_category (blog_article_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_27A7C64C9452A475 (blog_article_id), INDEX IDX_27A7C64C12469DE2 (category_id), PRIMARY KEY(blog_article_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, discriminator VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_article_category ADD CONSTRAINT FK_27A7C64C9452A475 FOREIGN KEY (blog_article_id) REFERENCES blog_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_article_category ADD CONSTRAINT FK_27A7C64C12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_article DROP FOREIGN KEY FK_EECCB3E5A76ED395');
        $this->addSql('DROP INDEX IDX_EECCB3E5A76ED395 ON blog_article');
        $this->addSql('ALTER TABLE blog_article ADD publisher VARCHAR(255) NOT NULL, ADD published TINYINT(1) DEFAULT NULL, ADD comments_on TINYINT(1) DEFAULT NULL, DROP user_id');
        $this->addSql('ALTER TABLE content ADD download_type VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE blog_article_category DROP FOREIGN KEY FK_27A7C64C12469DE2');
        $this->addSql('DROP TABLE blog_article_category');
        $this->addSql('DROP TABLE category');
        $this->addSql('ALTER TABLE blog_article ADD user_id INT DEFAULT NULL, DROP publisher, DROP published, DROP comments_on');
        $this->addSql('ALTER TABLE blog_article ADD CONSTRAINT FK_EECCB3E5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_EECCB3E5A76ED395 ON blog_article (user_id)');
        $this->addSql('ALTER TABLE content DROP download_type');
    }
}
