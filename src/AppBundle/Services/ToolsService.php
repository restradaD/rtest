<?php

namespace AppBundle\Services;

use Doctrine\ORM\Query;
use AppBundle\Entity\User;
use AppBundle\Entity\Device;
use AppBundle\Entity\Company;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Notification;
use AppBundle\Entity\NotificationType;
use AppBundle\Entity\NotificationChannel;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

/**
 * Class ToolsService
 * @package AppBundle\Services
 */
class ToolsService
{
    /** @var ContainerInterface $container */
    private $container;
    /** @var EntityManager $entityManager */
    private $entityManager;
    /** @var Router $router */
    private $router;

    /**
     * ToolsService constructor.
     * @param ContainerInterface $serviceContainer
     * @param UrlMatcherInterface $router
     */
	public function __construct(ContainerInterface $serviceContainer, UrlMatcherInterface $router)
    {
        $this->container = $serviceContainer;
        $this->entityManager = $this->container->get('doctrine')->getManager();
        $this->router = $router;
    }

    /**
     * Return API Key.
     * @param int $length = 10
     * @return string
     * */
    public function generateApiKey($length = 32)
    {
        $possible = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRESTUVWXYZ_";
        if ($length == "" OR !is_numeric($length)){
            $length = 8;
        }

        srand($this->make_seed());

        $i = 0;
        $apikey = "";
        while ($i < $length) {
            $char = substr($possible, rand(0, strlen($possible)-1), 1);
            if (!strstr($apikey, $char)) {
                $apikey .= $char;
                $i++;
            }
        }

        if ($this->checkIfApiKeyExists($apikey)) {
            return $this->generateApiKey($length);
        }

        return $apikey;
    }

    /**
     * Checks if given apikey exists.
     * @param string $apikey
     * @return bool
     * */
    protected function checkIfApiKeyExists($apikey)
    {
        $entityManager = $this->entityManager;

        $exists = $entityManager->getRepository('AppBundle:User')
            ->findOneBy(['apiKey' => $apikey]);

        return $exists ? true : false;
    }

    /**
     * Creates seed for srand function
     * @return int
     * */
    protected function make_seed()
    {
        list($usec, $sec) = explode(' ', microtime());
        return $sec + $usec * 1000000;
    }

    /**
     * @param $key
     * @param null $default
     * @param mixed $currentCompany
     * @return null
     */
    public function get($key, $default = null, $currentCompany = null)
    {
        if (!$currentCompany) {
            /** @var Company $currentCompany */
            $currentCompany = $this->getCurrentCompany();
        }

        /** @var EntityManager $entityManager */
        $entityManager = $this->entityManager;

        $settings = $entityManager->createQueryBuilder()
            ->select('s')
            ->from('AppBundle:Settings', 's')
            ->where('s.company = :company')
            ->setParameter('company', $currentCompany)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_ARRAY)
        ;

        return isset($settings[$key]) ? $settings[$key] : $default;
    }

    /**
     * Return current company.
     * @return Company
     * */
    public function getCurrentCompany()
    {
        /** @var User $currentUser */
        $currentUser = $this->getCurrentUser();

        /** @var Company $currentCompany */
        $currentCompany = $currentUser->getCompany();

        if (!is_object($currentCompany)) {
            throw new LogicException('Invalid company.');
        }

        return $currentCompany;
    }

    /**
     * Return current user.
     * @return User | boolean
     * */
    public function getCurrentUser()
    {
        $token = $this->container->get('security.token_storage')->getToken();

        if (!$token) {
            return false;
        }

        /** @var \Symfony\Component\Security\Core\User\User $user */
        $user = $token->getUser();

        if (!is_object($user)) {
            return false;
        }

        /** @var User $currentUser */
        $currentUser = $this->entityManager->getRepository('AppBundle:User')
            ->findOneValidBy(['username' => $user->getUsername()]);

        if (!is_object($currentUser)) {
            return false;
        }

        return $currentUser;
    }

    /**
     * Return current company user list.
     * @return array
     * */
    public function getCurrentCompanyUserList()
    {
        /** @var Company $company */
        $company = $this->getCurrentCompany();

        $users = $this->entityManager->getRepository('AppBundle:User')
            ->findBy(['company' => $company]);

        return $users;
    }

    /**
     * Return if user can receive notification type by channel
     * @param User $currentUser
     * @param NotificationType $notificationType
     * @param NotificationChannel $notificationChannel
     * @return boolean
     */
    public function canSendNotificationTypeByChannel(User $currentUser, NotificationType $notificationType, NotificationChannel $notificationChannel)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->entityManager;

        $criteria = [
            'notificationType' => $notificationType,
            'notificationChannel' => $notificationChannel,
            'userPreferences' => $currentUser->getPreferences()
        ];

        $setup = $entityManager->getRepository('AppBundle:NotificationPreferences')
            ->findOneBy($criteria);

        return boolval($setup);
    }

    /**
     * Send notification via Channel
     * @param Notification $notification
     * @param NotificationChannel $channel
     * @return boolean
     */
    public function notifyByChannel(Notification $notification, NotificationChannel $channel){
        $id = $channel->getId();

        /** Linking notifications to channels if relationship does not exists */
        if (!in_array($channel, $notification->getNotificationChannels()->toArray())) {
            $notification->addNotificationChannel($channel);
            $this->entityManager->flush();
        }

        if (NotificationChannel::APP === $id) {
            return $this->notifyViaSystem($notification);
        }

        if (NotificationChannel::EMAIL === $id) {
            return $this->notifyViaEmail($notification);
        }

        if (NotificationChannel::GCM === $id) {
            return $this->notifyViaMobile($notification);
        }

        if (NotificationChannel::SMS === $id) {
            return $this->notifyViaSMS($notification);
        }

        return false;
    }

    /**
     * Notification entity to Array
     * @param Notification $notification
     * @return array
     */
    protected function notificationToArray(Notification $notification)
    {
        /** @var User $sender */
        $sender = $notification->getFrom();

        /** @var User receiver */
        $receiver = $notification->getTo();

        return [
            'id' => $notification->getId(),
            'title' => $notification->getTitle(),
            'description' => $notification->getDescription(),
            'date' => $notification->getCreatedAt()->format('c'),
            'url' => $this->router->generate('app_notification_redirect', ['id' => $notification->getId()], UrlGenerator::ABSOLUTE_URL),
            'type' => [
                'id' => $notification->getNotificationType()->getId()
            ],
            'sender' => [
                'id' => $sender->getId(),
                'name' => $sender->getFullName(),
                'initials' => $sender->getInitial(),
                'email' => $sender->getEmail(),
                'locale' => $sender->getLocale(),
                'picture' => $sender->getPicture(),
            ],
            'receiver' => [
                'id' => $receiver->getId(),
                'name' => $receiver->getFullName(),
                'initials' => $receiver->getInitial(),
                'email' => $receiver->getEmail(),
                'locale' => $receiver->getLocale(),
                'picture' => $receiver->getPicture(),
            ],
        ];
    }

    /**
     * Send notification via System notifications
     * @param Notification $notification
     * @return bool
     */
    protected function notifyViaSystem(Notification $notification)
    {
        try {
            $registrationIds = [];
            $user = $notification->getTo();
            $data = $this->notificationToArray($notification);

            $devices = $this->getDevices($user);

            foreach ($devices as $device) {
                /** @var Device $device */
                $registrationIds[] = $device->getUuid();
            }

            if (count($registrationIds) > 0) {
                $response = $this->container->get('app.fcm')->send($data, $registrationIds);
                return $response;
            } else {
                return 'No registered devices.';
            }

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Return users devices array.
     * @param User $user
     * @return ArrayCollection
     */
    private function getDevices(User $user)
    {
        $entityManager = $this->entityManager;

        return $entityManager->getRepository('AppBundle:Device')
            ->findBy(['user' => $user]);
    }

    /**
     * Send notification via Email
     * @param Notification $notification
     * @return bool
     */
    protected function notifyViaEmail(Notification $notification)
    {
        $company = $notification->getTo()->getCompany();

        $from = $this->get('email', null, $company);
        $subject = $notification->getTitle();
        $email = $notification->getTo()->getEmail();
        $data = $this->notificationToArray($notification);
        $twig = $this->container->get('twig');
        $transport = null;

        try {
            $mailerTransport = $this->get('mailerTransport', null, $company);
            $mailerHost = $this->get('mailerHost', null, $company);
            $mailerUser = $this->get('mailerUsername', null, $company);
            $mailerPassword = $this->get('mailerPassword', null, $company);
            $mailerPort = $this->get('mailerPort', null, $company);
            $mailerEncryption = $this->get('mailerEncryption', null, $company);
            $mailerAuthMode = $this->get('mailerAuthMode', null, $company);

            if ('smtp' === $mailerTransport) {
                $transport = \Swift_SmtpTransport::newInstance($mailerHost, $mailerPort);
                $transport->setUsername($mailerUser);
                $transport->setPassword($mailerPassword);

                if ($mailerEncryption) { $transport->setEncryption($mailerEncryption); }
                if ($mailerAuthMode) { $transport->setAuthMode($mailerAuthMode); }
            }

            if ('mail' === $mailerTransport) {
                $transport = \Swift_MailTransport::newInstance($mailerHost);
            }

            if ('sendmail' === $mailerTransport) {
                $transport = \Swift_SendmailTransport::newInstance($mailerHost);
            }

            if ($transport) {
                $mailer = \Swift_Mailer::newInstance($transport);

                $message = \Swift_Message::newInstance('Mailer')
                    ->setSubject($subject)
                    ->setFrom($from)
                    ->setTo($email)
                    ->setBody(
                        $twig->render(
                            'Components/Emails/notification.html.twig',
                            $data
                        ),
                        'text/html'
                    )
                    ->addPart($twig->render('Components/Emails/notification.txt.twig', $data ), 'text/plain')
                ;

                return $mailer->send($message);
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Send notification via Mobile notifications
     * @param Notification $notification
     * @return bool
     */
    protected function notifyViaMobile(Notification $notification)
    {
        return false;
    }

    /**
     * Send notification via SMS
     * @param Notification $notification
     * @return bool
     */
    protected function notifyViaSMS(Notification $notification)
    {
        return false;
    }

    /**
     * @param Notification $notification
     * @return array
     */
    public function sendNotification(Notification $notification)
    {
        $entityManager = $this->entityManager;
        $type = $notification->getNotificationType();

        $notification->setResponse(json_encode(['status' => 'pending']));
        $entityManager->flush();

        $channels = $entityManager->getRepository('AppBundle:NotificationChannel')
            ->findAll();

        $status = [];

        foreach ($channels as $channel) {
            /** @var NotificationChannel $channel */
            if ($this->container->get('app.tools')->canSendNotificationTypeByChannel($notification->getTo(), $type, $channel)) {
                $status[$channel->getName()] = $this->container->get('app.tools')->notifyByChannel($notification, $channel);
            }
        }

        $notification->setResponse(json_encode($status));
        $entityManager->flush();

        return $status;
    }
}
