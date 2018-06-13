<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadUserData
 * @package AppBundle\DataFixtures\ORM
 */
class LoadUsersData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        /** ----------------------------------- **/
        $super = $userManager->createUser();
        $super->setUsername('super');
        $super->setEmail('rene1tres@gmail.com');
        $super->setPlainPassword('rene');
        $super->setEnabled(true);
        $super->setCompany($this->getReference('rtest'));
        $super->setRoles(array('ROLE_SUPER_ADMIN', 'ROLE_TRANSLATOR'));
        $super->setApiKey($this->container->get('app.tools')->generateApiKey());
        $super->setLocale('es');
        $super->setFirstName('Super');
        $super->setLastName('Admin');

        $userManager->updateUser($super, true);
        $this->addReference('super-user', $super);
        /** ----------------------------------- **/
        $admin = $userManager->createUser();
        $admin->setUsername('admin');
        $admin->setEmail('admin@rtest.com');
        $admin->setPlainPassword('rene');
        $admin->setEnabled(true);
        $admin->setCompany($this->getReference('rtest'));
        $admin->setRoles(array('ROLE_ADMIN', 'ROLE_TRANSLATOR'));
        $admin->setApiKey($this->container->get('app.tools')->generateApiKey());
        $admin->setLocale('es');
        $admin->setFirstName('Admin');
        $admin->setLastName('User');

        $userManager->updateUser($admin, true);
        $this->addReference('admin-user', $admin);
        /** ----------------------------------- **/
        /** ----------------------------------- **/
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 15;
    }
}