<?php

namespace AppBundle\Controller\API;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\NotificationChannel;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Entity\NotificationPreferences;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 *
 * NotificationChannelController API Class
 * @Route("/api/{version}/notification-channels")
 *
 * */
class NotificationChannelController extends FOSRestController
{
    /**
     * Get Notifications Channel list.
     * @param ParamFetcherInterface $paramFetcher
     * @param Request $request
     *
     * @Route(
     *     ".{_format}",
     *     name="api_notification_channel_list",
     *     defaults={"_format": "json", "version": "v1"},
     *     methods={"GET"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get notification channel list.",
     *     views = { "default" },
     *     section="Channels"
     * )
     *
     * @QueryParam(name="limit", requirements="\d+", default=10, nullable=true, description="Query limit")
     * @QueryParam(name="page", requirements="\d+", default=1, nullable=true, description="Page")
     * @QueryParam(name="sort_field", default="createdAt", nullable=true, description="Sort field")
     * @QueryParam(name="sort", default="DESC", nullable=true, description="Sort direction")
     * @QueryParam(name="search", default="", nullable=true, description="Text to filter...")
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     *
     * */
    public function indexAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        $search = $paramFetcher->get('search');
        $limit = $paramFetcher->get('limit');
        $page = $paramFetcher->get('page');
        $sortField = $paramFetcher->get('sort_field');
        $sort = $paramFetcher->get('sort');

        /** API version */
        $version = $request->get('version');

        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        $queryBuilder = $entityManager->getRepository('AppBundle:NotificationChannel')
            ->findAllQueryBuilder($search, $sortField, $sort);

        $paginatedCollection = $this->get('app.pagination_factory')
            ->createCollection($queryBuilder, $search, $page, $limit, 'api_notification_channel_list', [ 'sort_field' => $sortField, 'sort' => $sort, 'version' => $version ]);

        $view = $this->view($paginatedCollection);

        return $this->handleView($view);
    }

    /**
     * Get notification channel.
     * @param NotificationChannel $notificationChannel
     * @return Response
     * @Route(
     *     "/{id}.{_format}",
     *     name="api_notification_channel_show",
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get notification channel.",
     *     views = { "default" },
     *     section="Channels"
     * )
     *
     * */
    public function showAction(NotificationChannel $notificationChannel)
    {
        $view = $this->view($notificationChannel);

        return $this->handleView($view);
    }

    /**
     * Get notification setup for notification channels and notification types.
     * @param Request $request
     * @return Response
     * @Route(
     *     "/{channel_id}/notification-types/{type_id}.{_format}",
     *     name="api_notification_channel_set_types",
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get notification setup for notification channels and notification types.",
     *     views = { "default" },
     *     section="Channels"
     * )
     *
     * */
    public function setSetupAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $channel_id = $request->get('channel_id');
        $type_id = $request->get('type_id');

        $user = $this->get('app.tools')->getCurrentUser();

        $channel = $entityManager->getRepository('AppBundle:NotificationChannel')
            ->find($channel_id);

        $type = $entityManager->getRepository('AppBundle:NotificationType')
            ->find($type_id);

        $setup = new NotificationPreferences();
        $setup->setNotificationType($type);
        $setup->setNotificationChannel($channel);
        $setup->setUserPreferences($user->getPreferences());

        $entityManager->persist($setup);
        $entityManager->flush();

        $data = ['active' => 1];

        $view = $this->view($data);

        return $this->handleView($view);
    }

    /**
     * Get notification setup for notification channels and notification types.
     * @param Request $request
     * @return Response
     * @Route(
     *     "/{channel_id}/notification-types/{type_id}.{_format}",
     *     name="api_notification_channel_delete_types",
     *     defaults={"_format": "json"},
     *     methods={"DELETE"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get notification setup for notification channels and notification types.",
     *     views = { "default" },
     *     section="Channels"
     * )
     *
     * */
    public function deleteSetupAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $channel_id = $request->get('channel_id');
        $type_id = $request->get('type_id');

        $user = $this->get('app.tools')->getCurrentUser();

        $channel = $entityManager->getRepository('AppBundle:NotificationChannel')
            ->find($channel_id);

        $type = $entityManager->getRepository('AppBundle:NotificationType')
            ->find($type_id);

        $setup = $entityManager->getRepository('AppBundle:NotificationPreferences')
            ->findOneBy([ 'notificationChannel' => $channel, 'notificationType' => $type, 'userPreferences' => $user->getPreferences() ]);

        $entityManager->remove($setup);
        $entityManager->flush();

        $view = $this->view([], Response::HTTP_NO_CONTENT);

        return $this->handleView($view);
    }
}