<?php

namespace AdminBundle\Services;

use Trinity\AdminBundle\Event\MenuEvent;

/**
 * This listener extends the Venice MenuListener.
 *
 * Class MenuListener
 */
class MenuListener
{
    /**
     * @param MenuEvent $event
     *
     * @throws \InvalidArgumentException
     * @throws \Trinity\AdminBundle\Exception\MenuException
     */
    public function onMenuConfigure(MenuEvent $event)
    {
        $menu = $event->getMenu('sidebar');

        $menu
            ->addChild('Child menu item', ['route' => 'admin_product_index'])
            ->setAttribute('icon', 'trinity trinity-home')
            ->setExtra('orderNumber', 5);
    }
}
