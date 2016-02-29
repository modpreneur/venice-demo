<?php

namespace AppBundle;

/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 26.02.16
 * Time: 15:36
 */
class AppBundle extends \Symfony\Component\HttpKernel\Bundle\Bundle
{
    public function getParent()
    {
        return "VeniceAppBundle";
    }
}