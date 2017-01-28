<?php

use Bmatzner\JQueryBundle\BmatznerJQueryBundle;
use Cocur\HumanDate\Bridge\Symfony\CocurHumanDateBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Uran1980\FancyBoxBundle\Uran1980FancyBoxBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;

/**
 * Class AppKernel
 */
class AppKernel extends Venice\AppBundle\Kernel\VeniceKernel
{
    /**
     * @return array|\Symfony\Component\HttpKernel\Bundle\BundleInterface[]
     */
    public function registerBundles()
    {
        $veniceBundles = parent::registerBundles();

        $veniceBundles[] = new \AppBundle\AppBundle();
        $veniceBundles[] = new \AdminBundle\AdminBundle();
        $veniceBundles[] = new \BunnyBundle\BunnyBundle();
        $veniceBundles[] = new \FrontBundle\FrontBundle();
        $veniceBundles[] = new \DataTransferBundle\DataTransferBundle();

        // others ------------
        $veniceBundles[] = new CocurHumanDateBundle();
        $veniceBundles[] = new Uran1980FancyBoxBundle;
        $veniceBundles[] = new BmatznerJQueryBundle();
        $veniceBundles[] = new DoctrineMigrationsBundle();
        $veniceBundles[] = new FlofitEntities\Bundle\FlofitEntitiesBundle\FlofitEntitiesBundle();
       // $veniceBundles[] = new Amazon();


        return $veniceBundles;
    }


    /**
     * Boots the current kernel.
     *
     * @api
     */
    public function boot()
    {
        parent::boot();

        if ($this->container->has('flofit_amazon_s3')) {
            /** @var \Aws\S3\S3Client $s3client */
            $s3client = $this->container->get('flofit_amazon_s3');
            $s3client->registerStreamWrapper();
        }
    }



    /**
     * @return string
     */
    public function getRootDir()
    {
        return __DIR__;
    }


    /**
     * @return string
     */
    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }


    /**
     * @return string
     */
    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }


    /**
     * @param LoaderInterface $loader
     *
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
