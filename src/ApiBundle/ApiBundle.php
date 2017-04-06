<?php
namespace ApiBundle;

use ApiBundle\Api\ApiFactory;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ApiBundle
 * @package ApiBundle
 */
class ApiBundle extends Bundle
{

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var SecurityExtension $extension */
        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new ApiFactory());
    }
}
