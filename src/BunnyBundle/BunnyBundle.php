<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 26.02.16
 * Time: 15:36
 */

namespace BunnyBundle;


use Symfony\Component\HttpKernel\Bundle\Bundle;

class BunnyBundle extends Bundle
{
    public function getParent()
    {
        return 'VeniceBunnyBundle';
    }
}