<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Settings;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadSettingsData
 * @package AppBundle\DataFixtures\ORM
 */
class LoadSettingsData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $settings = new Settings();
        $settings->setEmail('rene1tres@gmail.com');
        $settings->setUrl('http://127.0.0.1:8000/');
        $settings->setApiUrl('http://127.0.0.1:8000/api/v1/');
        $settings->setCompany($this->getReference('rtest'));

        $manager->persist($settings);
        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 11;
    }
}