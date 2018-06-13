<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Notification;
use Doctrine\Common\EventSubscriber;
use AppBundle\Entity\NotificationType;
use Symfony\Component\Process\Process;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NotificationsSubscriber
 * @package AppBundle\EventListener
 */
class NotificationsSubscriber implements EventSubscriber
{
    const CREATED = 1;
    const UPDATED = 0;

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
        return ['postPersist','postUpdate'];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->proxy($args, self::CREATED);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->proxy($args, self::UPDATED);
    }

    /**
     * Decides if given entity applies for this subscriber
     * @param LifecycleEventArgs $args
     * @param integer $action
     */
    protected function proxy(LifecycleEventArgs $args, $action)
    {
        // $entityManager = $args->getEntityManager();
        $entity = $args->getEntity();

        /**
         * Example only.
         *
        if ($entity instanceof Task) {
            $task = $entity;
            $action = $action ? NotificationType::TASK_CREATED : NotificationType::TASK_UPDATED;

            $this->notifyTasks($task, $entityManager, $action);
        }
        */

        /** Executes only on creation */
        if (self::CREATED == $action) {
            if ($entity instanceof Notification) {
                $command = 'cd ' . $this->container->get('kernel')->getRootDir() . '/../ && php bin/console app:push-notifications';

                $process = new Process($command);
                $process->start();
            }
        }
    }

    /**
     * Creates notification entity.
     * @param NotificationType $type
     * @param User $to
     * @param $title
     * @param $description
     * @param null $path
     * @param array $parameters
     * @param null $url
     */
    protected function createNotification(NotificationType $type, User $to, $title, $description, $path = null, $parameters = array(), $url = null)
    {
        /** @var User $currentUser */
        $currentUser = $this->container->get('app.tools')->getCurrentUser();

        /** Avoiding sending notification to currentUser */
        if ($currentUser !== $to) {
            /** @var EntityManager $entityManager */
            $entityManager = $this->container->get('doctrine')->getManager();

            $notification = new Notification();
            $notification->setNotificationType($type);
            $notification->setFrom($currentUser);
            $notification->setTo($to);
            $notification->setTitle($title);
            $notification->setDescription($description);
            $notification->setPath($path);
            $notification->setParameters($parameters);
            $notification->setUrl($url);
            $notification->setSeen(false);
            $notification->setChecked(false);

            $entityManager->persist($notification);
            $entityManager->flush();
        }
    }

    /**
     * Init notify service for Task (Example only)
     * @param EntityManager $entityManager
     * @param integer $action
     *
    protected function notifyTasks(Task $task, EntityManager $entityManager, $action)
    {

        $currentUser = $this->container->get('app.tools')->getCurrentUser();


        $repository = $entityManager->getRepository('Gedmo\Translatable\Entity\Translation');
        $type = $this->retrieveNotificationType($action, $entityManager);
        $translations = $repository->findTranslations($type);

        $users = $task->getUsers();

        foreach ($users as $user) {
            $locale = $user->getLocale() ? $user->getLocale() : $this->container->getParameter('locale');
            $title = isset($translations[$locale]) ? $translations[$locale]['name'] : $type->getName();
            $textTemplate = isset($translations[$locale]) ? $translations[$locale]['textTemplate'] : $type->getTextTemplate();

            $description = str_replace('%user%', (String)$currentUser, $textTemplate);
            $description = str_replace('%title%', $task->getName(), $description);

            $this->createNotification($type, $user, $title, $description, 'app_calendar');
        }
    }
    */


    /**
     * @param $notificationTypeId
     * @param EntityManager $entityManager
     * @return NotificationType
     */
    protected function retrieveNotificationType($notificationTypeId, EntityManager $entityManager)
    {
        return $entityManager->getRepository('AppBundle:NotificationType')
            ->find($notificationTypeId);
    }
}