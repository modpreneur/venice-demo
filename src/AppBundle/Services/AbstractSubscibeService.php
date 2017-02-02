<?php

namespace AppBundle\Services;

use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AbstractSubscibeService
 * @package AppBundle\Services
 */
abstract class AbstractSubscibeService extends \Twig_Extension
{
    /** @var ContainerInterface  */
    protected $serviceContainer;

    protected $parameters;

    protected $initCodeIsRendered = false;


    /**
     * AbstractSubscibeService constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->serviceContainer = $container;

        $this->parameters = $this->loadParameters();
    }


    /**
     * @return string|\Symfony\Bundle\TwigBundle\TwigEngine
     */
    public function renderInit()
    {
        if ($this->initCodeIsRendered) {
            return '';
        } else {
            $this->initCodeIsRendered = true;

            return $this->render('VeniceFrontBundle:' .$this->getName(). ':init.html.twig');
        }
    }


    /**
     * @param User $user
     *
     * @return bool
     */
    abstract public function haveSubscription(User $user);


    /**
     * @param User $user
     *
     * @return mixed
     */
    abstract public function unsubscribe(User $user);

    /**
     * @return array
     */
    abstract protected function loadParameters();


    /**
     * @return array
     */
    public function getGlobals()
    {
        return [
            $this->getName() => $this
        ];
    }


    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_Function($this->getName() . 'Render', [$this, 'renderBlock'], ['is_safe' => ['html']])
        ];
    }


    /**
     * @param $blockName
     *
     * @return mixed
     */
    public function renderBlock($blockName)
    {
        $parameters = func_get_args();

        if (count($parameters) > 1) {
            return call_user_func([$this, 'render' .$blockName], array_slice($parameters, 1)[0]);
        }

        return call_user_func([$this, 'render' .$blockName]);
    }


    /**
     * @param $name
     * @param array $parameters
     *
     * @return \Symfony\Bundle\TwigBundle\TwigEngine
     * @throws \Twig_Error
     * @throws \RuntimeException
     */
    protected function render($name, array $parameters = [])
    {
        return $this
            ->serviceContainer
            ->get('templating')->render($name, array_merge(['appParameters' =>$this->parameters], $parameters));
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        $thisClass = new \ReflectionClass($this);

        return $thisClass->getShortName();
    }
}
