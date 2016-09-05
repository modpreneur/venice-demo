<?php

namespace AdminBundle;

/**
 * Class AdminBundle.
 */
class AdminBundle extends \Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @return string
     */
    public function getParent()
    {
        return 'VeniceAdminBundle';
    }
}
