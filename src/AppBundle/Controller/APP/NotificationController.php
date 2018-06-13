<?php

namespace AppBundle\Controller\APP;

use AppBundle\Entity\User;
use AppBundle\Entity\Notification;
use AppBundle\Security\CheckOwnershipVoter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class NotificationController
 * @package AppBundle\Controller\APP
 * @Route("/notifications")
 */
class NotificationController extends Controller
{
    /**
     * Show all notifications
     * @return Response
     * @Route("/", name="app_notification_list")
     */
    public function indexAction()
    {
        return $this->render('APP/Notifications/index.html.twig');
    }

    /**
     * Notification setup
     * @return Response
     * @Route("/setup", name="app_notification_setup")
     * */
    public function setupAction()
    {
        return $this->render('APP/Notifications/setup.html.twig');
    }

    /**
     * Redirect notification.
     * @param Notification $notification
     * @return Response
     * @Route("/{id}", name="app_notification_redirect", options={"expose" = true})
     * */
    public function showAction(Notification $notification)
    {
        $this->denyAccessUnlessGranted(CheckOwnershipVoter::OWNER, $notification);
        $this->touchNotification($notification);

        $url = $notification->getUrl();

        if (!empty($url)) {
            return $this->redirect($url);
        }

        /** @var User $user */
        $user = $notification->getTo();
        $locale = $user->getLocale() ? $user->getLocale() : $this->getParameter('locale');

        $path = $notification->getPath();
        $parameters = $notification->getParameters();

        $parameters['_locale'] = $locale;

        return $this->redirectToRoute($path, $parameters);
    }

    /**
     * Touch a notification
     * @param Notification $notification
     */
    protected function touchNotification(Notification &$notification)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $notification->setSeen(true);
        $notification->setChecked(true);

        $entityManager->flush();
    }

}