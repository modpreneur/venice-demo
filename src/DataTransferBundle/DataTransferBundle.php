<?php
namespace DataTransferBundle;

/**
 * Class DataTransferBundle
 */
class DataTransferBundle extends \Symfony\Component\HttpKernel\Bundle\Bundle
{
    public function getParent()
    {
        return 'FlofitEntitiesBundle';
    }
}
