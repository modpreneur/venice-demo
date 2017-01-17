<?php
namespace FrontBundle;

/**
 * Class FrontBundle
 * @package FrontBundle
 */
class FrontBundle extends \Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @return string
     */
    public function getParent()
    {
        return 'VeniceFrontBundle';
    }
}
