<?php

use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Venice\AppBundle\Kernel\VeniceKernel
{
    public function registerBundles()
    {
        $veniceBundles = parent::registerBundles();

        $veniceBundles[] = new \AppBundle\AppBundle();
        $veniceBundles[] = new \AdminBundle\AdminBundle();
        $veniceBundles[] = new \BunnyBundle\BunnyBundle();

        return $veniceBundles;
    }


    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
