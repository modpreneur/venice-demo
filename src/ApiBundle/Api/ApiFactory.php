<?php

namespace ApiBundle\Api;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;


/**
 * Class ApiFactory
 * @package ApiBundle\Api
 */
class ApiFactory implements SecurityFactoryInterface
{
    /**
     * @param ContainerBuilder $container
     * @param string $id
     * @param array $config
     * @param string $userProvider
     * @param string $defaultEntryPoint
     *
     * @return array
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.api.' . $id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('api.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProvider));

        $listenerId = 'security.authentication.listener.api.' . $id;
        $listener = $container->setDefinition($listenerId,
            new DefinitionDecorator('api.security.authentication.listener'));

        return [$providerId, $listenerId, $defaultEntryPoint];
    }


    /**
     * @return string
     */
    public function getPosition()
    {
        return 'pre_auth';
    }


    /**
     * @return string
     */
    public function getKey()
    {
        return 'api';
    }


    /**
     * @param NodeDefinition $node
     */
    public function addConfiguration(NodeDefinition $node)
    {
    }
}
