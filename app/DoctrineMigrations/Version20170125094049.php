<?php

namespace VeniceApp\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170125094049 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE newsletter_optimization_answer (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, answer LONGTEXT DEFAULT NULL, tag VARCHAR(255) DEFAULT NULL, list_id INT NOT NULL, INDEX IDX_88163D8D1E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter_optimalization_question (id INT AUTO_INCREMENT NOT NULL, question VARCHAR(500) NOT NULL, successful_redirect VARCHAR(500) NOT NULL, multiple TINYINT(1) NOT NULL, page INT NOT NULL, `Order` INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter_optimalization_user_answer (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, answer_id INT DEFAULT NULL, user_id INT DEFAULT NULL, clicked TINYINT(1) NOT NULL, timestamp DATETIME NOT NULL, INDEX IDX_C7842AEF1E27F6BF (question_id), INDEX IDX_C7842AEFAA334807 (answer_id), INDEX IDX_C7842AEFA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE newsletter_optimization_answer ADD CONSTRAINT FK_88163D8D1E27F6BF FOREIGN KEY (question_id) REFERENCES newsletter_optimalization_question (id)');
        $this->addSql('ALTER TABLE newsletter_optimalization_user_answer ADD CONSTRAINT FK_C7842AEF1E27F6BF FOREIGN KEY (question_id) REFERENCES newsletter_optimalization_question (id)');
        $this->addSql('ALTER TABLE newsletter_optimalization_user_answer ADD CONSTRAINT FK_C7842AEFAA334807 FOREIGN KEY (answer_id) REFERENCES newsletter_optimization_answer (id)');
        $this->addSql('ALTER TABLE newsletter_optimalization_user_answer ADD CONSTRAINT FK_C7842AEFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE newsletter_optimalization_user_answer DROP FOREIGN KEY FK_C7842AEFAA334807');
        $this->addSql('ALTER TABLE newsletter_optimization_answer DROP FOREIGN KEY FK_88163D8D1E27F6BF');
        $this->addSql('ALTER TABLE newsletter_optimalization_user_answer DROP FOREIGN KEY FK_C7842AEF1E27F6BF');
        $this->addSql('DROP TABLE newsletter_optimization_answer');
        $this->addSql('DROP TABLE newsletter_optimalization_question');
        $this->addSql('DROP TABLE newsletter_optimalization_user_answer');
    }
}
