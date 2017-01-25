<?php

namespace VeniceApp\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170117132900m extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("
            INSERT INTO product (id, necktie_default_billing_plan_id, venice_default_billing_plan_id, group_id, handle, image, enabled, order_number, name, description, discriminator, necktie_id, purchasable, necktie_description, standard_product_child, upsell_order, upsel_miniature, upsel_miniature_mobile, is_recommended, short_description, short_name, free_product_child, client_product_child) VALUES
            (1,	NULL,	NULL,	1,	'flofit',	'',	1,	0,	'FLO FIT',	'sd',	'demo_standard_product',	NULL,	NULL,	NULL,	NULL,	NULL,	'https://s3-us-west-2.amazonaws.com/flofit-prod/site-resources/core_2D.jpg',	NULL,	1,	'Get your platinum body with the ultimate mix of FLO FIT workouts.',	'Platinum Mix',	NULL,	NULL)
        ");

        $this->addSql("
            INSERT INTO product (id, necktie_default_billing_plan_id, venice_default_billing_plan_id, group_id, handle, image, enabled, order_number, name, description, discriminator, necktie_id, purchasable, necktie_description, standard_product_child, upsell_order, upsel_miniature, upsel_miniature_mobile, is_recommended, short_description, short_name, free_product_child, client_product_child) VALUES
            (2,	NULL,	NULL,	2,	'platinumclub',	NULL,	1,	0,	'Platinum Club',	NULL,	'demo_standard_product',	NULL,	NULL,	NULL,	NULL,	NULL,	'https://cdn.flofit.com/site-resources/platinumclub_d_2D.jpg',	'https://s3-us-west-2.amazonaws.com/flofit-prod/site-resources/flomersion_2D.jpg',	1,	'Your backstage pass to exclusive videos, workouts, and nutrition.',	'Platinum Club',	NULL,	NULL)
        ");


        $this->addSql("
            INSERT INTO product (id, necktie_default_billing_plan_id, venice_default_billing_plan_id, group_id, handle, image, enabled, order_number, name, description, discriminator, necktie_id, purchasable, necktie_description, standard_product_child, upsell_order, upsel_miniature, upsel_miniature_mobile, is_recommended, short_description, short_name, free_product_child, client_product_child) VALUES
            (3,	NULL,	NULL,	NULL,	'7-day-rip-mix',	NULL,	1,	0,	'7 Day Rip Mix',	NULL,	'demo_standard_product',	NULL,	NULL,	NULL,	NULL,	NULL,	'https://cdn.flofit.com/site-resources/7_day.png',	'https://s3-us-west-2.amazonaws.com/flofit-prod/site-resources/7_day.jpg',	1,	'Get amazing results in just 7 days!',	'7 Day Rip Mix',	NULL,	NULL)
        ");

        $this->addSql("
            INSERT INTO product (id, necktie_default_billing_plan_id, venice_default_billing_plan_id, group_id, handle, image, enabled, order_number, name, description, discriminator, necktie_id, purchasable, necktie_description, standard_product_child, upsell_order, upsel_miniature, upsel_miniature_mobile, is_recommended, short_description, short_name, free_product_child, client_product_child) VALUES
            (4,	NULL,	NULL,	NULL,	'nutrition-and-meals',	NULL,	1,	0,	'Nutrition and Meals',	NULL,	'demo_standard_product',	NULL,	NULL,	NULL,	NULL,	NULL,	'https://cdn.flofit.com/site-resources/nutrition_2D.png',	'https://s3-us-west-2.amazonaws.com/flofit-prod/site-resources/nutrition_2D.jpg',	1,	'Get step by step meal plans and recipes for your 8 week program.',	'Nutrition and Meals',	NULL,	NULL)
        ");
    }


    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
