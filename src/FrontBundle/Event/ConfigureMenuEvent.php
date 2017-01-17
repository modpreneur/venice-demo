<?php

namespace FrontBundle\Event;

use Doctrine\ORM\EntityManager;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ConfigureMenuEvent
 * @package FrontBundle\Event
 */
class ConfigureMenuEvent extends Event
{
    const CONFIGURE = 'front.menu_configure_event';

    private $factory;
    private $menu;
    private $pathInfo;
    private $routeName;
    private $userLogged;

    private $doctrineEntityManager;

    /**
     * @param \Knp\Menu\FactoryInterface $factory
     * @param \Knp\Menu\ItemInterface $menu
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Doctrine\ORM\EntityManager $manager
     * @param $isUserLogged
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu, Request $request,EntityManager $manager, $isUserLogged)
    {
        $this->factory = $factory;
        $this->menu = $menu;
        $this->pathInfo = $request->getPathInfo();
        $this->routeName = $request->get("_route");
        $this->userLogged = $isUserLogged;

        $this->doctrineEntityManager = $manager;
    }

    public function getPathInfo()
    {
        return $this->pathInfo;
    }

    public function pathStartWith($name)
    {
        $length = strlen($name);
        return (substr($this->pathInfo, 0, $length) == $name);
    }

    public function isActualRoute($name)
    {
        return $this->routeName == $name;
    }

    public function isActualRouteIn(array $array)
    {
        foreach ($array as $item) {
            if ($item == $this->routeName) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return \Knp\Menu\FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @return \Knp\Menu\ItemInterface | null
     */
    public function getAdministrationMenu()
    {
        foreach ($this->menu->getChildren() as $child) {
            /** @var MenuItem $child */
            if ($child->getName() == "Administration")
                return $child;
        }
        return null;
    }

    public function isUserLogged()
    {
        return $this->userLogged;
    }

    public function getDoctrineEntityManager()
    {
        return $this->doctrineEntityManager;
    }
}