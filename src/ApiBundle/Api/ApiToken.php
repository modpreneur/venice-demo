<?php

namespace ApiBundle\Api;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * Class ApToken
 * @package ApiBundle\Api
 */
class ApiToken extends AbstractToken
{

    /** @var  \DateTime */
    public $created;


    /**
     * ApToken constructor.
     *
     * @param array $roles
     */
    public function __construct(array $roles = [])
    {
        parent::__construct($roles);

        // If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);
    }


    /**
     * Returns the user credentials.
     *
     * @return mixed The user credentials
     */
    public function getCredentials()
    {
        return '';
    }
}
