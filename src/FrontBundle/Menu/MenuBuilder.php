<?php

namespace FrontBundle\Menu;

use Doctrine\ORM\EntityManager;
use FrontBundle\Event\ConfigureMenuEvent;
use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class MenuBuilder
 * @package Venice\FrontBundle\Menu
 */
class MenuBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;


    /** @var FactoryInterface */
    private $factory;


    /**
     * @param FactoryInterface $factory
     *
     * Add any other dependency you need
     * @param ContainerInterface $container
     */
    public function __construct(FactoryInterface $factory, ContainerInterface $container)
    {
        $this->factory   = $factory;
        $this->container = $container;
    }


    /**
     *
     * @param RequestStack $requestStack
     *
     * @return \Knp\Menu\ItemInterface
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function build(RequestStack $requestStack)
    {
        $factory = $this->factory;

        /** @var Request $request */
        $request = $requestStack->getCurrentRequest();

        $menu = $factory->createItem('root');

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $menu->addChild('Administration', ['route' => 'admin_dashboard']);
        }

        $isUserLogged = $this->container->get('security.token_storage')->getToken()->getUser() !== 'anon.';

        /** @var EntityManager $entityManager */
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        $this->container->get('event_dispatcher')->dispatch(
            ConfigureMenuEvent::CONFIGURE,
            new ConfigureMenuEvent($factory, $menu, $request, $entityManager, $isUserLogged)
        );


        // ... blech
        foreach ($menu->getChildren() as $item) {
            $item->setCurrent(
                $item->getUri() === $requestStack->getCurrentRequest()->getRequestUri()
            );

            if (!$item->isCurrent() && $item->hasChildren()) {
                foreach ($item->getChildren() as $child) {
                    $item->setCurrent(
                        $child->getUri() === $requestStack->getCurrentRequest()->getRequestUri()
                    );
                }
            }
        }

        return $menu;
    }
}
