<?php

namespace AppBundle\Menu;

use AppBundle\Entity\Company;
use AppBundle\Entity\Permission;
use AppBundle\Entity\User;
use AppBundle\Security\PrimaryVoter;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MenuBuilder
{
	/**
	 * @var FactoryInterface $factory
	 */
	private $factory;

	/**
	 * @var ContainerInterface $container
	 */
	private $container;

	/**
	 * @param FactoryInterface $factory
	 * @param ContainerInterface $container
	 *
	 * Add any other dependency you need
	 */
	public function __construct(FactoryInterface $factory, ContainerInterface $container)
	{
		$this->factory = $factory;
		$this->container = $container;
	}

	public function mainMenu(array $options)
    {
		$menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'nav metismenu')
            ->setChildrenAttribute('id', 'side-menu');

        $this->configureDashboardMenu($menu);
        $this->configureSuperAdministratorMenu($menu);
        $this->configureAdministratorMenu($menu);
        $this->configureAPPMenu($menu);
        $this->configureUsersMenu($menu);
        $this->configureSettingsMenu($menu);

        return $menu;
    }

    /**
     * @param $menu
     */
    public function configureDashboardMenu(ItemInterface &$menu)
    {
        $checker = $this->container->get('security.authorization_checker');

        if ($checker->isGranted(User::ROLE_ADMIN)) {
            $menu
                ->addChild('app.app.dashboard', ['route' => 'administrator_dashboard'])
                ->setAttribute('icon', 'fa fa-th-large');
        }

        if ($checker->isGranted(User::ROLE_APP)) {
            $menu
                ->addChild('app.app.dashboard', ['route' => 'app_homepage'])
                ->setAttribute('icon', 'fa fa-th-large');
        }
    }

    /**
     * @param $menu
     */
    public function configureAdministratorMenu(ItemInterface &$menu)
    {
    }

    /**
     * @param $menu
     */
    public function configureSuperAdministratorMenu(ItemInterface &$menu)
    {
        $checker = $this->container->get('security.authorization_checker');

        // Permission list
        if ($checker->isGranted(PrimaryVoter::MANAGE, Permission::class)) {

        }
    }

    /**
     * @param $menu
     */
    public function configureAPPMenu(ItemInterface &$menu)
    {
        $checker = $this->container->get('security.authorization_checker');

        if ($checker->isGranted(User::ROLE_APP)) {
            // TODO: Build menu
        }
    }

    /**
     * @param $menu
     */
    public function configureSettingsMenu(ItemInterface &$menu)
    {
        $checker = $this->container->get('security.authorization_checker');

        if ($checker->isGranted(User::ROLE_ADMIN)) {
        }

    }

    /**
     * @param $menu
     */
    public function configureUsersMenu(ItemInterface &$menu)
    {
        $checker = $this->container->get('security.authorization_checker');

        if ($checker->isGranted(PrimaryVoter::MANAGE, User::class)) {
            $menu
                ->addChild('app.users', ['uri' => '#'])
                ->setAttribute('dropdown', true)
                ->setAttribute('icon', 'fa fa-group')
                ->setChildrenAttribute('class', 'nav nav-second-level collapse');

            $menu['app.users']
                ->addChild('app.administrator.users.list', ['route' => 'administrator_user_list'])
                ->setExtra('routes', ['administrator_user_list', 'administrator_user_edit', 'app_user_profile']);
        }
    }
}
