<?php

namespace VeniceApp\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170119130603 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');


        $this->addSql("INSERT INTO content (id, author_id, name, created_at, updated_at, discriminator, handle, html, link, duration, preview_image, video_mob, video_lq, video_hq, video_hd, group_content_child, html_content_child, iframe_content_child, mp3content_child, pdf_content_child, video_content_child, vimeo_thumbnail_id, need_gear, file_protected) VALUES 
            (3,	NULL,	'Workout Guide Book',	'2017-01-01 00:00:00',	'2017-01-01 00:00:00',	'demo_pdf_content',	'workout-guide-book',	NULL,	'https://s3.amazonaws.com/flofit-products/FloFit_workout_guide.pdf',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	'https://products.flofit.com/FloFit_workout_guide.pdf')
        ");


        $this->addSql("INSERT INTO content (id, author_id, name, created_at, updated_at, discriminator, handle, html, link, duration, preview_image, video_mob, video_lq, video_hq, video_hd, group_content_child, html_content_child, iframe_content_child, mp3content_child, pdf_content_child, video_content_child, vimeo_thumbnail_id, need_gear, file_protected) VALUES 
            (4,	NULL,	'8 Week Platinum Calendar',	'2017-01-01 00:00:00',	'2017-01-01 00:00:00',	'demo_pdf_content',	'8 Week Platinum Calendar',	NULL,	'https://s3.amazonaws.com/flofit-products/Flofit_8_week_platinum_workout_calendar.pdf',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	'https://products.flofit.com/Flofit_8_week_platinum_workout_calendar.pdf')
        ");


        $this->addSql("INSERT INTO content (id, author_id, name, created_at, updated_at, discriminator, handle, html, link, duration, preview_image, video_mob, video_lq, video_hq, video_hd, group_content_child, html_content_child, iframe_content_child, mp3content_child, pdf_content_child, video_content_child, vimeo_thumbnail_id, need_gear, file_protected) VALUES 
            (5,	NULL,	'Nutrition Guide',	'2017-01-01 00:00:00',	'2017-01-01 00:00:00',	'demo_pdf_content',	'nutrition-guide',	NULL,	'https://s3.amazonaws.com/flofit-products/FloFit_nutrition_guide.pdf',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	'https://products.flofit.com/FloFit_nutrition_guide.pdf')
        ");

        $this->addSql("INSERT INTO content (id, author_id, name, created_at, updated_at, discriminator, handle, html, link, duration, preview_image, video_mob, video_lq, video_hq, video_hd, group_content_child, html_content_child, iframe_content_child, mp3content_child, pdf_content_child, video_content_child, vimeo_thumbnail_id, need_gear, file_protected) VALUES 
            (6,	NULL,	'8 Week Master Mix Meal Plan',	'2017-01-01 00:00:00',	'2017-01-01 00:00:00',	'demo_pdf_content',	'8-week-master-mix-meal-plan',	NULL,	'https://s3.amazonaws.com/flofit-products/FloFit_8_week_master_mix_meal_plan.pdf',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	'https://products.flofit.com/FloFit_8_week_master_mix_meal_plan.pdf')
        ");

        $this->addSql("INSERT INTO content (id, author_id, name, created_at, updated_at, discriminator, handle, html, link, duration, preview_image, video_mob, video_lq, video_hq, video_hd, group_content_child, html_content_child, iframe_content_child, mp3content_child, pdf_content_child, video_content_child, vimeo_thumbnail_id, need_gear, file_protected) VALUES 
           (7,	NULL,	'7 Day Workout Calendar',	'2017-01-01 00:00:00',	'2017-01-01 00:00:00',	'demo_pdf_content',	'7-day-workout-calendar',	NULL,	'https://s3.amazonaws.com/flofit-products/FloFit_7_day_rip_mix_calendar.pdf',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	'https://products.flofit.com/FloFit_7_day_rip_mix_calendar.pdf') 
        ");

        $this->addSql("INSERT INTO content (id, author_id, name, created_at, updated_at, discriminator, handle, html, link, duration, preview_image, video_mob, video_lq, video_hq, video_hd, group_content_child, html_content_child, iframe_content_child, mp3content_child, pdf_content_child, video_content_child, vimeo_thumbnail_id, need_gear, file_protected) VALUES 
            (8,	NULL,	'7 Day Meal Plan',	'2017-01-01 00:00:00',	'2017-01-01 00:00:00',	'demo_pdf_content',	'7-day-meal-plan',	NULL,	'https://s3.amazonaws.com/flofit-products/FloFit_7_day_meal_plan.pdf',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	'https://products.flofit.com/FloFit_7_day_meal_plan.pdf')
        ");

        $this->addSql("INSERT INTO content (id, author_id, name, created_at, updated_at, discriminator, handle, html, link, duration, preview_image, video_mob, video_lq, video_hq, video_hd, group_content_child, html_content_child, iframe_content_child, mp3content_child, pdf_content_child, video_content_child, vimeo_thumbnail_id, need_gear, file_protected) VALUES 
            (9,	NULL,	'Beginner 8 Week Calendar',	'2017-01-01 00:00:00',	'2017-01-01 00:00:00',	'demo_pdf_content',	'beg-8-week-calendars',	NULL,	'https://s3.amazonaws.com/flofit-products/FloFit_workout_guide_calendar_beginner.pdf',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	'https://products.flofit.com/FloFit_workout_guide_calendar_beginner.pdf')
        ");

        $this->addSql("INSERT INTO content (id, author_id, name, created_at, updated_at, discriminator, handle, html, link, duration, preview_image, video_mob, video_lq, video_hq, video_hd, group_content_child, html_content_child, iframe_content_child, mp3content_child, pdf_content_child, video_content_child, vimeo_thumbnail_id, need_gear, file_protected) VALUES 
            (10,	NULL,	'Intermediate 8 Week Calendar',	'2017-01-01 00:00:00',	'2017-01-01 00:00:00',	'demo_pdf_content',	'int-8-week-calendars',	NULL,	'https://s3.amazonaws.com/flofit-products/FloFit_workout_guide_calendar_inter.pdf',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	'https://products.flofit.com/FloFit_workout_guide_calendar_inter.pdf')
        ");

        $this->addSql("INSERT INTO content (id, author_id, name, created_at, updated_at, discriminator, handle, html, link, duration, preview_image, video_mob, video_lq, video_hq, video_hd, group_content_child, html_content_child, iframe_content_child, mp3content_child, pdf_content_child, video_content_child, vimeo_thumbnail_id, need_gear, file_protected) VALUES 
            (11,	NULL,	'Advanced 8 Week Calendar',	'2017-01-01 00:00:00',	'2017-01-01 00:00:00',	'demo_pdf_content',	'adv-8-week-calendars',	NULL,	'https://s3.amazonaws.com/flofit-products/FloFit_workout_guide_calendar_advanced.pdf',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	'https://products.flofit.com/FloFit_workout_guide_calendar_advanced.pdf')
        ");

        $this->addSql("INSERT INTO content (id, author_id, name, created_at, updated_at, discriminator, handle, html, link, duration, preview_image, video_mob, video_lq, video_hq, video_hd, group_content_child, html_content_child, iframe_content_child, mp3content_child, pdf_content_child, video_content_child, vimeo_thumbnail_id, need_gear, file_protected) VALUES 
            (12,	NULL,	'Bonus Breakfast, Lunch, Dinner and Snack Recipes #1',	'2017-01-01 00:00:00',	'2017-01-01 00:00:00',	'demo_pdf_content',	'bonus-breakfast-lunch-dinner-and-snack-recipes-1',	NULL,	'https://s3.amazonaws.com/flofit-products/Module-1-Meals-and-Recipes.pdf',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	'https://products.flofit.com/Module-1-Meals-and-Recipes.pdf')
        ");

        $this->addSql("INSERT INTO content (id, author_id, name, created_at, updated_at, discriminator, handle, html, link, duration, preview_image, video_mob, video_lq, video_hq, video_hd, group_content_child, html_content_child, iframe_content_child, mp3content_child, pdf_content_child, video_content_child, vimeo_thumbnail_id, need_gear, file_protected) VALUES 
            (13,	NULL,	'Quick Start Guide',	'2017-01-01 00:00:00',	'2017-01-01 00:00:00',	'demo_pdf_content',	'quick-start-guide',	NULL,	'https://s3.amazonaws.com/flofit-products/quick_start_guide.pdf',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	'https://products.flofit.com/quick_start_guide.pdf')
        ");

        $this->addSql("INSERT INTO content (id, author_id, name, created_at, updated_at, discriminator, handle, html, link, duration, preview_image, video_mob, video_lq, video_hq, video_hd, group_content_child, html_content_child, iframe_content_child, mp3content_child, pdf_content_child, video_content_child, vimeo_thumbnail_id, need_gear, file_protected) VALUES 
            (14,	NULL,	'Bonus Breakfast, Lunch, Dinner and Snack Recipes #2',	'2017-01-01 00:00:00',	'2017-01-01 00:00:00',	'demo_pdf_content',	'bonus-breakfast-lunch-dinner-and-snack-recipes-2',	NULL,	'https://s3.amazonaws.com/flofit-products/Module-2-Meals-and-Recipes.pdf',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	'https://products.flofit.com/Module-2-Meals-and-Recipes.pdf')
        ");

        $this->addSql("
            INSERT INTO contents_products (id, content_id, product_id, delay, order_number, created_at, updated_at, discriminator, content_product_child) VALUES
            (3,	13,	1,	0,	0,	'2017-01-18 20:37:37',	'2017-01-18 20:37:37',	'venice_contentproduct',	NULL)
        ");

        $this->addSql("
            INSERT INTO contents_products (id, content_id, product_id, delay, order_number, created_at, updated_at, discriminator, content_product_child) VALUES
            (4,	9,	1,	0,	1,	'2017-01-18 20:38:05',	'2017-01-18 20:38:05',	'venice_contentproduct',	NULL)
        ");

        $this->addSql("
            INSERT INTO contents_products (id, content_id, product_id, delay, order_number, created_at, updated_at, discriminator, content_product_child) VALUES
            (5,	10,	1,	0,	2,	'2017-01-18 20:38:22',	'2017-01-18 20:38:22',	'venice_contentproduct',	NULL)
        ");

        $this->addSql("
            INSERT INTO contents_products (id, content_id, product_id, delay, order_number, created_at, updated_at, discriminator, content_product_child) VALUES
            (6,	11,	1,	0,	0,	'2017-01-19 12:49:08',	'2017-01-19 12:49:08',	'venice_contentproduct',	NULL)
        ");

        $this->addSql("
            INSERT INTO contents_products (id, content_id, product_id, delay, order_number, created_at, updated_at, discriminator, content_product_child) VALUES
            (7,	3,	1,	0,	0,	'2017-01-19 12:51:49',	'2017-01-19 12:51:49',	'venice_contentproduct',	NULL)
        ");

        $this->addSql("
            INSERT INTO contents_products (id, content_id, product_id, delay, order_number, created_at, updated_at, discriminator, content_product_child) VALUES
            (8,	5,	1,	0,	0,	'2017-01-19 12:52:12',	'2017-01-19 12:52:12',	'venice_contentproduct',	NULL)
        ");

        $this->addSql("
            INSERT INTO contents_products (id, content_id, product_id, delay, order_number, created_at, updated_at, discriminator, content_product_child) VALUES
            (9,	6,	1,	0,	0,	'2017-01-19 12:52:47',	'2017-01-19 12:52:47',	'venice_contentproduct',	NULL)
        ");

        $this->addSql("
            INSERT INTO contents_products (id, content_id, product_id, delay, order_number, created_at, updated_at, discriminator, content_product_child) VALUES
            (10,	7,	1,	0,	0,	'2017-01-19 12:54:57',	'2017-01-19 12:54:57',	'venice_contentproduct',	NULL)
        ");

        $this->addSql("
            INSERT INTO contents_products (id, content_id, product_id, delay, order_number, created_at, updated_at, discriminator, content_product_child) VALUES
            (11,	8,	1,	0,	0,	'2017-01-19 12:55:19',	'2017-01-19 12:55:19',	'venice_contentproduct',	NULL)
        ");

        $this->addSql("
            INSERT INTO contents_products (id, content_id, product_id, delay, order_number, created_at, updated_at, discriminator, content_product_child) VALUES
            (12,	4,	1,	0,	0,	'2017-01-19 12:56:03',	'2017-01-19 12:56:03',	'venice_contentproduct',	NULL)
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
