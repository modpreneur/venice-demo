<?php

namespace AdminBundle;

/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 26.02.16
 * Time: 15:36
 */
class AdminBundle extends \Symfony\Component\HttpKernel\Bundle\Bundle
{
    public function getParent()
    {
        return "VeniceAdminBundle";
    }
}