<?php

namespace FrontBundle\Menu;

use FrontBundle\Event\ConfigureMenuEvent;
use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MenuBuilder
 * @package Venice\FrontBundle\Menu
 */
class MenuBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;


    public function build(FactoryInterface $factory, array $options)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        /** @var Request $request */

        $menu = $factory->createItem('root');
        // $menu->setCurrentUri($request->getRequestUri());

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $menu->addChild('Administration', ['route' => 'admin_dashboard']);
        }

        $isUserLogged = $this->container->get('security.token_storage')->getToken()->getUser() != "anon.";

        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        $this->container->get('event_dispatcher')->dispatch(
            ConfigureMenuEvent::CONFIGURE,
            new ConfigureMenuEvent($factory, $menu, $request, $entityManager, $isUserLogged)
        );

        return $menu;
    }
}
