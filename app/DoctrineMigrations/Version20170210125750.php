<?php

namespace VeniceApp\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170210125750 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE blog_article_category (blog_article_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_27A7C64C9452A475 (blog_article_id), INDEX IDX_27A7C64C12469DE2 (category_id), PRIMARY KEY(blog_article_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_article_tag (blog_article_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_48A608079452A475 (blog_article_id), INDEX IDX_48A60807BAD26311 (tag_id), PRIMARY KEY(blog_article_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, handle VARCHAR(255) NOT NULL, discriminator VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), UNIQUE INDEX UNIQ_64C19C1918020D9 (handle), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, handle VARCHAR(255) NOT NULL, discriminator VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_389B7835E237E06 (name), UNIQUE INDEX UNIQ_389B783918020D9 (handle), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_article_category ADD CONSTRAINT FK_27A7C64C9452A475 FOREIGN KEY (blog_article_id) REFERENCES blog_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_article_category ADD CONSTRAINT FK_27A7C64C12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_article_tag ADD CONSTRAINT FK_48A608079452A475 FOREIGN KEY (blog_article_id) REFERENCES blog_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_article_tag ADD CONSTRAINT FK_48A60807BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_article ADD published TINYINT(1) DEFAULT NULL, ADD comments_on TINYINT(1) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE blog_article_category DROP FOREIGN KEY FK_27A7C64C12469DE2');
        $this->addSql('ALTER TABLE blog_article_tag DROP FOREIGN KEY FK_48A60807BAD26311');
        $this->addSql('DROP TABLE blog_article_category');
        $this->addSql('DROP TABLE blog_article_tag');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE tag');
        $this->addSql('ALTER TABLE blog_article DROP published, DROP comments_on');
    }
}
