<?php

namespace VeniceApp\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170117073154 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE billing_plan (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, pay_system_vendor_id INT DEFAULT NULL, necktie_id INT NOT NULL, price VARCHAR(50) NOT NULL, initial_price NUMERIC(7, 2) NOT NULL, rebill_price NUMERIC(7, 2) DEFAULT NULL, frequency SMALLINT DEFAULT NULL, rebill_times SMALLINT DEFAULT NULL, trial SMALLINT DEFAULT NULL, discriminator VARCHAR(255) NOT NULL, billing_plan_child VARCHAR(255) DEFAULT NULL, is_recurring TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_A22865BA7CEC66E1 (necktie_id), INDEX IDX_A22865BA4584665A (product_id), INDEX IDX_A22865BAD8E034B3 (pay_system_vendor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_article (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, handle VARCHAR(255) NOT NULL, date_to_publish DATETIME DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, discriminator VARCHAR(255) NOT NULL, blog_article_child VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_EECCB3E5918020D9 (handle), INDEX IDX_EECCB3E5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_article_product (blog_article_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_8C613C999452A475 (blog_article_id), INDEX IDX_8C613C994584665A (product_id), PRIMARY KEY(blog_article_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, discriminator VARCHAR(255) NOT NULL, handle VARCHAR(255) DEFAULT NULL, html LONGTEXT DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, duration INT DEFAULT NULL, preview_image VARCHAR(255) DEFAULT NULL, video_mob VARCHAR(255) DEFAULT NULL, video_lq VARCHAR(255) DEFAULT NULL, video_hq VARCHAR(255) DEFAULT NULL, video_hd VARCHAR(255) DEFAULT NULL, group_content_child VARCHAR(255) DEFAULT NULL, html_content_child VARCHAR(255) DEFAULT NULL, iframe_content_child VARCHAR(255) DEFAULT NULL, mp3content_child VARCHAR(255) DEFAULT NULL, pdf_content_child VARCHAR(255) DEFAULT NULL, video_content_child VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_FEC530A95E237E06 (name), INDEX IDX_FEC530A9F675F31B (author_id), UNIQUE INDEX UNIQ_FEC530A9918020D9 (handle), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_in_group (id INT AUTO_INCREMENT NOT NULL, group_id INT DEFAULT NULL, content_id INT DEFAULT NULL, delay INT NOT NULL, order_number INT NOT NULL, discriminator VARCHAR(255) NOT NULL, content_in_group_child VARCHAR(255) DEFAULT NULL, INDEX IDX_7582B054FE54D947 (group_id), INDEX IDX_7582B05484A0A3ED (content_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contents_products (id INT AUTO_INCREMENT NOT NULL, content_id INT DEFAULT NULL, product_id INT DEFAULT NULL, delay INT NOT NULL, order_number INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, discriminator VARCHAR(255) NOT NULL, content_product_child VARCHAR(255) DEFAULT NULL, INDEX IDX_F8FCB46784A0A3ED (content_id), INDEX IDX_F8FCB4674584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth_token (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, access_token VARCHAR(255) NOT NULL, refresh_token VARCHAR(255) NOT NULL, valid_to DATETIME NOT NULL, scope VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, discriminator VARCHAR(255) NOT NULL, oauth_token_child VARCHAR(255) DEFAULT NULL, INDEX IDX_D8344B2AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pay_system (id INT AUTO_INCREMENT NOT NULL, default_vendor_id INT DEFAULT NULL, necktie_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, discriminator VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_40D6DBC47CEC66E1 (necktie_id), UNIQUE INDEX UNIQ_40D6DBC45E237E06 (name), INDEX IDX_40D6DBC46761C6D4 (default_vendor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pay_system_vendor (id INT AUTO_INCREMENT NOT NULL, default_vendor_id INT DEFAULT NULL, necktie_id INT DEFAULT NULL, default_for_venice TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, discriminator VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D11B51C57CEC66E1 (necktie_id), UNIQUE INDEX UNIQ_D11B51C55E237E06 (name), INDEX IDX_D11B51C56761C6D4 (default_vendor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, necktie_default_billing_plan_id INT DEFAULT NULL, venice_default_billing_plan_id INT DEFAULT NULL, group_id INT DEFAULT NULL, handle VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, order_number INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, discriminator VARCHAR(255) NOT NULL, necktie_id INT DEFAULT NULL, purchasable TINYINT(1) DEFAULT NULL, necktie_description VARCHAR(255) DEFAULT NULL, standard_product_child VARCHAR(255) DEFAULT NULL, upsell_order INT DEFAULT NULL, upsel_miniature VARCHAR(255) DEFAULT NULL, upsel_miniature_mobile VARCHAR(255) DEFAULT NULL, is_recommended TINYINT(1) DEFAULT NULL, short_description LONGTEXT DEFAULT NULL, short_name VARCHAR(20) DEFAULT NULL, free_product_child VARCHAR(255) DEFAULT NULL, client_product_child VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D34A04AD918020D9 (handle), UNIQUE INDEX UNIQ_D34A04AD5E237E06 (name), UNIQUE INDEX UNIQ_D34A04AD7CEC66E1 (necktie_id), INDEX IDX_D34A04ADB72FA94 (necktie_default_billing_plan_id), INDEX IDX_D34A04AD4A92D3BE (venice_default_billing_plan_id), INDEX IDX_D34A04ADFE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_access (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, product_id INT DEFAULT NULL, necktie_id INT NOT NULL, from_date DATETIME NOT NULL, to_date DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, discriminator VARCHAR(255) NOT NULL, product_access_child VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_64156AF07CEC66E1 (necktie_id), INDEX IDX_64156AF0A76ED395 (user_id), INDEX IDX_64156AF04584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', necktie_id INT DEFAULT NULL, preferred_units VARCHAR(10) NOT NULL, date_of_birth DATE NOT NULL, locked TINYINT(1) NOT NULL, discriminator VARCHAR(255) NOT NULL, user_child VARCHAR(255) DEFAULT NULL, first_name VARCHAR(50) DEFAULT NULL, last_name VARCHAR(50) DEFAULT NULL, phone_number VARCHAR(22) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, public TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_8D93D649C05FB297 (confirmation_token), UNIQUE INDEX UNIQ_8D93D6497CEC66E1 (necktie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cron_task (id INT AUTO_INCREMENT NOT NULL, command VARCHAR(255) NOT NULL, creation_time DATETIME NOT NULL, processing_time DATETIME DEFAULT NULL, priority INT NOT NULL, status VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trinity_settings (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, owner_id VARCHAR(255) DEFAULT NULL, group_name VARCHAR(64) DEFAULT NULL, UNIQUE INDEX unique_name_owner_id_group_name (name, owner_id, group_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, handle VARCHAR(100) NOT NULL, order_number INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE billing_plan ADD CONSTRAINT FK_A22865BA4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE billing_plan ADD CONSTRAINT FK_A22865BAD8E034B3 FOREIGN KEY (pay_system_vendor_id) REFERENCES pay_system_vendor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_article ADD CONSTRAINT FK_EECCB3E5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blog_article_product ADD CONSTRAINT FK_8C613C999452A475 FOREIGN KEY (blog_article_id) REFERENCES blog_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_article_product ADD CONSTRAINT FK_8C613C994584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT FK_FEC530A9F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE content_in_group ADD CONSTRAINT FK_7582B054FE54D947 FOREIGN KEY (group_id) REFERENCES content (id)');
        $this->addSql('ALTER TABLE content_in_group ADD CONSTRAINT FK_7582B05484A0A3ED FOREIGN KEY (content_id) REFERENCES content (id)');
        $this->addSql('ALTER TABLE contents_products ADD CONSTRAINT FK_F8FCB46784A0A3ED FOREIGN KEY (content_id) REFERENCES content (id)');
        $this->addSql('ALTER TABLE contents_products ADD CONSTRAINT FK_F8FCB4674584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE oauth_token ADD CONSTRAINT FK_D8344B2AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pay_system ADD CONSTRAINT FK_40D6DBC46761C6D4 FOREIGN KEY (default_vendor_id) REFERENCES pay_system_vendor (id)');
        $this->addSql('ALTER TABLE pay_system_vendor ADD CONSTRAINT FK_D11B51C56761C6D4 FOREIGN KEY (default_vendor_id) REFERENCES pay_system (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADB72FA94 FOREIGN KEY (necktie_default_billing_plan_id) REFERENCES billing_plan (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD4A92D3BE FOREIGN KEY (venice_default_billing_plan_id) REFERENCES billing_plan (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADFE54D947 FOREIGN KEY (group_id) REFERENCES product_group (id)');
        $this->addSql('ALTER TABLE product_access ADD CONSTRAINT FK_64156AF0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_access ADD CONSTRAINT FK_64156AF04584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADB72FA94');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD4A92D3BE');
        $this->addSql('ALTER TABLE blog_article_product DROP FOREIGN KEY FK_8C613C999452A475');
        $this->addSql('ALTER TABLE content_in_group DROP FOREIGN KEY FK_7582B054FE54D947');
        $this->addSql('ALTER TABLE content_in_group DROP FOREIGN KEY FK_7582B05484A0A3ED');
        $this->addSql('ALTER TABLE contents_products DROP FOREIGN KEY FK_F8FCB46784A0A3ED');
        $this->addSql('ALTER TABLE pay_system_vendor DROP FOREIGN KEY FK_D11B51C56761C6D4');
        $this->addSql('ALTER TABLE billing_plan DROP FOREIGN KEY FK_A22865BAD8E034B3');
        $this->addSql('ALTER TABLE pay_system DROP FOREIGN KEY FK_40D6DBC46761C6D4');
        $this->addSql('ALTER TABLE billing_plan DROP FOREIGN KEY FK_A22865BA4584665A');
        $this->addSql('ALTER TABLE blog_article_product DROP FOREIGN KEY FK_8C613C994584665A');
        $this->addSql('ALTER TABLE contents_products DROP FOREIGN KEY FK_F8FCB4674584665A');
        $this->addSql('ALTER TABLE product_access DROP FOREIGN KEY FK_64156AF04584665A');
        $this->addSql('ALTER TABLE blog_article DROP FOREIGN KEY FK_EECCB3E5A76ED395');
        $this->addSql('ALTER TABLE content DROP FOREIGN KEY FK_FEC530A9F675F31B');
        $this->addSql('ALTER TABLE oauth_token DROP FOREIGN KEY FK_D8344B2AA76ED395');
        $this->addSql('ALTER TABLE product_access DROP FOREIGN KEY FK_64156AF0A76ED395');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADFE54D947');
        $this->addSql('DROP TABLE billing_plan');
        $this->addSql('DROP TABLE blog_article');
        $this->addSql('DROP TABLE blog_article_product');
        $this->addSql('DROP TABLE content');
        $this->addSql('DROP TABLE content_in_group');
        $this->addSql('DROP TABLE contents_products');
        $this->addSql('DROP TABLE oauth_token');
        $this->addSql('DROP TABLE pay_system');
        $this->addSql('DROP TABLE pay_system_vendor');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_access');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE cron_task');
        $this->addSql('DROP TABLE trinity_settings');
        $this->addSql('DROP TABLE product_group');
    }
}
