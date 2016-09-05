<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 26.02.16
 * Time: 15:36.
 */
namespace AppBundle;

class AppBundle extends \Symfony\Component\HttpKernel\Bundle\Bundle
{
    public function getParent()
    {
        return 'VeniceAppBundle';
    }
}
