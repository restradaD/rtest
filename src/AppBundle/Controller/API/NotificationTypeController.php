<?php

namespace AppBundle\Controller\API;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\NotificationType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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
 * NotificationTypeController API Class
 * @Route("/api/{version}/notification-types")
 *
 * */
class NotificationTypeController extends FOSRestController
{
    /**
     * Get Notifications Types list.
     * @param ParamFetcherInterface $paramFetcher
     * @param Request $request
     *
     * @Route(
     *     ".{_format}",
     *     name="api_notification_type_list",
     *     defaults={"_format": "json", "version": "v1"},
     *     methods={"GET"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get notification types list.",
     *     views = { "default" },
     *     section="Notification types"
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

        $queryBuilder = $entityManager->getRepository('AppBundle:NotificationType')
            ->findAllQueryBuilder($search, $sortField, $sort);

        $paginatedCollection = $this->get('app.pagination_factory')
            ->createCollection($queryBuilder, $search, $page, $limit, 'api_notification_type_list', [ 'sort_field' => $sortField, 'sort' => $sort, 'version' => $version ]);

        $view = $this->view($paginatedCollection);

        return $this->handleView($view);
    }

    /**
     * Get notification type.
     * @param NotificationType $notificationType
     * @return Response
     * @Route(
     *     "/{id}.{_format}",
     *     name="api_show_notification_type_show",
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get notification type.",
     *     views = { "default" },
     *     section="Notification types"
     * )
     *
     * */
    public function showAction(NotificationType $notificationType)
    {
        $view = $this->view($notificationType);

        return $this->handleView($view);
    }
}