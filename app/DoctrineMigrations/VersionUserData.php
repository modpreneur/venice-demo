<?php

namespace VeniceApp\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class VersionUserData extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $columns = '
            INSERT INTO newsletter_optimalization_question (id, question, successful_redirect, multiple, page, `Order`) VALUES
        ';

        $this->addSql($columns . " (1,	'Gender',	'core_front_user_profile_edit',	0,	1,	0)");
        $this->addSql($columns . " (2,	'Age',	'core_front_user_profile_edit',	0,	3,	0)");
        $this->addSql($columns . " (3,	'Children',	'core_front_user_profile_edit',	0,	1,	0)");
        $this->addSql($columns . " (4,	'Newsletter Optimalizations',	'core_front_user_profile_newsletters',	1,	2,	0)");


        $columns = '
            INSERT INTO newsletter_optimization_answer (id, question_id, answer, tag, list_id) VALUES
        ';

        $this->addSql($columns . " (2,	1,	'Male',	'gender_male',	0)");
        $this->addSql($columns . " (3,	1,	'Female','gender_female',	0)");
        $this->addSql($columns . " (4,	2,	'<20',	'age_under_21',	0)");
        $this->addSql($columns . " (5,	2,	'21-30','age_21_30',	0)");
        $this->addSql($columns . " (6,	2,	'31-40','age_31_40',	0)");
        $this->addSql($columns . " (7,	2,	'41-50','age_41_50',	0)");
        $this->addSql($columns . " (8,	2,	'51-60','age_51_60',	0)");
        $this->addSql($columns . " (9,	2,	'>60',	'age_above_60',	0)");
        $this->addSql($columns . " (10,	3,	'Yes, I have children living at home.',	'kids_home',	0)");
        $this->addSql($columns . " (11,	3,	'Yes, but my kids are all moved out.',	'kids_moved',	0)");
        $this->addSql($columns . " (12,	3,	'Nope, I don\'t have any kids.',	'kids_no',	0)");
        $this->addSql($columns . " (13,	4,	'Weight loss ',	'opt_weight_loss',	0)");
        $this->addSql($columns . " (14,	4,	'Building muscle',	'opt_building_muscle',	0)");
        $this->addSql($columns . " (15,	4,	'General fitness',	'opt_general_fitness',	0)");
        $this->addSql($columns . " (16,	4,	'Disease prevention',	'opt_disease_prevention',	0)");
        $this->addSql($columns . " (17,	4,	'Alternative health treatments','opt_alternative_health_treatments',0)");
        $this->addSql($columns . " (18,	4,	'Recipes & food prep tips',	'opt_recipes_and_food_prep_tips',	0)");
    }


    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this
            ->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
