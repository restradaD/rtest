<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\UserPreferences;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\NotificationPreferences;

/**
 * Class UserCreationSubscriber
 * @package AppBundle\EventSubscriber
 */
class UserCreationSubscriber implements EventSubscriber
{
    /** @var ContainerInterface $container */
    private $container;

    /**
     * NotificationsSubscriber constructor.
     * @param ContainerInterface $serviceContainer
     */
    public function __construct(ContainerInterface $serviceContainer)
    {
        $this->container = $serviceContainer;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return ['postPersist'];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            $user = $entity;
            $entityManager = $args->getEntityManager();

            $this->createNotificationPreferences($user, $entityManager);
            $this->persistApiKey($user, $entityManager);
        }
    }

    protected function persistApiKey(User $user, EntityManager $entityManager)
    {
        $apiKey = $this->container->get('app.tools')->generateApiKey();
        $user->setApiKey($apiKey);

        $entityManager->flush();
    }

    /**
     * @param User $user
     * @param EntityManager $entityManager
     */
    protected function createNotificationPreferences(User $user, EntityManager $entityManager)
    {
        $preferences = new UserPreferences();
        $preferences->setReceiveDailyMail(true);

        $user->setPreferences($preferences);

        $entityManager->persist($preferences);
        $entityManager->flush();

        $this->activateAllNotificationChannelsAndTypes($preferences, $entityManager);
    }

    /**
     * @param UserPreferences $preferences
     * @param EntityManager $entityManager
     */
    protected function activateAllNotificationChannelsAndTypes(UserPreferences $preferences, EntityManager $entityManager)
    {
        $channels = $entityManager->getRepository('AppBundle:NotificationChannel')
            ->findAll();

        $types = $entityManager->getRepository('AppBundle:NotificationType')
            ->findAll();

        foreach ($channels as $channel) {
            foreach ($types as $type) {
                $notificationPreferences = new NotificationPreferences();
                $notificationPreferences->setNotificationChannel($channel);
                $notificationPreferences->setUserPreferences($preferences);
                $notificationPreferences->setNotificationType($type);
                $entityManager->persist($notificationPreferences);
                $entityManager->flush();
            }
        }
    }
}