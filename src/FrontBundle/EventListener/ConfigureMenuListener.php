<?php

namespace FrontBundle\EventListener;

use FrontBundle\Event\ConfigureMenuEvent;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ConfigureMenuListener
 * @package FrontBundle\EventListener
 */
class ConfigureMenuListener
{
    const SUPER_ADMIN = "ROLE_SUPER_ADMIN";
    const MODIFICATION_LOG_ROLE = "ROLE_ADMIN_MODIFICATION_LOG";
    const MODIFICATION_USERS = "ROLE_ADMIN_USERS_EDIT";
    const SOCIAL_EDIT = "ROLE_ADMIN_SOCIAL_EDIT";
    const PAGES_EDIT = "ROLE_ADMIN_PAGES_EDIT";

    /**
     * ConfigureMenuListener constructor.
     */
    public function __construct(ContainerInterface $containerInterface)
    {
        $this->container = $containerInterface;
    }


    /**
     * @param ConfigureMenuEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild("Dashboard", ["route"=>"landing_page"]);

        $myProfileItem = $menu->addChild("Profile",array("route"=>"core_front_user_profile_edit"));

        $menu->addChild("Messages",array("uri"=>$this->container->getParameter("messages_url")));
        $menu->addChild("Forum",array("uri"=>$this->container->getParameter("forum_url")));
        $menu->addChild("Blog",array("route"=>"blog_index"));
        //$menu->addChild("Articles & Videos",array("route"=>"articles_and_videos_index"));

//		$myProfileItem->addChild("User profile", array("route"=>"core_front_user_profile"));
        $myProfileItem->addChild("Profile", array("route"=>"core_front_user_profile_edit"));
        $myProfileItem->addChild("User privacy", array("route"=>"core_front_user_profile_privacy"));
        $myProfileItem->addChild("Order history", array("route"=>"core_front_user_order_history"));
        $myProfileItem->addChild("Newsletters", array("route"=>"core_front_user_profile_newsletters"));

        if ($adminItem = $event->getAdministrationMenu()) {
            $this->configureAdministrationMenu($event, $adminItem);
        }
    }


    /**
     * @param ConfigureMenuEvent $event
     * @param ItemInterface $adminItem
     */
    private function configureAdministrationMenu(ConfigureMenuEvent $event, ItemInterface $adminItem)
    {
        $grantChecker = $this->container->get("security.authorization_checker");

        if ($grantChecker->isGranted(self::SUPER_ADMIN) || $grantChecker->isGranted(self::MODIFICATION_LOG_ROLE)) {
           // $adminItem->addChild("Modifications log", array("route"=>"core_admin_log"));
        }

        if ($grantChecker->isGranted(self::SUPER_ADMIN) || $grantChecker->isGranted(self::MODIFICATION_USERS)) {
         //   $adminItem->addChild("Users",array("route"=>"core_admin_users"));
        }

        if ($grantChecker->isGranted(self::SOCIAL_EDIT)) {
            $adminItem->addChild("Social accounts", ["route"=>"admin_social_site_index"]);
        }

        if ($grantChecker->isGranted(self::PAGES_EDIT)) {
            $adminItem->addChild("Static pages", ["route"=>"core_admin_static_pages"]);
        }
    }
}