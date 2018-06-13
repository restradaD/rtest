<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\NotificationChannel;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

/**
 * Class LoadNotificationChannelData
 * @package AppBundle\DataFixtures\ORM
 */
class LoadNotificationChannelData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $appChannel = new NotificationChannel();
        $appChannel->setId(1);
        $appChannel->setName('APP');

        $manager->persist($appChannel);
        /** ----------------------- **/

        $emailChannel = new NotificationChannel();
        $emailChannel->setId(2);
        $emailChannel->setName('Email');

        $manager->persist($emailChannel);
        /** ----------------------- **/

        $manager->flush();

    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 12;
    }
}