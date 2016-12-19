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
    }
}
