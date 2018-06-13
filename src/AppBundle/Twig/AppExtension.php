<?php

namespace AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AppExtension extends \Twig_Extension
{
    /** @var ContainerInterface $container */
    private $container;

    public function __construct(ContainerInterface $serviceContainer)
    {
        $this->container = $serviceContainer;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('locale_code', array($this, 'localeCodeFilter'))
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get', array($this, 'getFunction'))
        );
    }

    public function localeCodeFilter($locale)
    {
        $chunk = explode('_', $locale);
        return $chunk[0];
    }

    public function getFunction($key, $default = null)
    {
        return $this->container->get('app.tools')->get($key, $default);
    }
}
