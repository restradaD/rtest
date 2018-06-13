<?php

namespace AppBundle\Controller\API;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Notification;
use AppBundle\Form\API\NotificationType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Security\CheckOwnershipVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 *
 * NotificationController API Class
 * @Route("/api/{version}/notifications")
 *
 * */
class NotificationController extends FOSRestController
{
    /**
     * Get Notifications list.
     * @param ParamFetcherInterface $paramFetcher
     * @param Request $request
     *
     * @Route(
     *     ".{_format}",
     *     name="api_notification_list",
     *     defaults={"_format": "json", "version": "v1"},
     *     methods={"GET"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get notification list.",
     *     views = { "default" },
     *     section="Notifications"
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

        /** @var \AppBundle\Entity\User $currentUser */
        $currentUser = $this->get('app.tools')->getCurrentUser();

        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        $queryBuilder = $entityManager->getRepository('AppBundle:Notification')
            ->findAllQueryBuilder($search, $sortField, $sort)->andWhere('n.to = :currentUser')
            ->setParameter('currentUser', $currentUser)
        ;

        $paginatedCollection = $this->get('app.pagination_factory')
            ->createCollection($queryBuilder, $search, $page, $limit, 'api_notification_list', [ 'sort_field' => $sortField, 'sort' => $sort, 'version' => $version ]);

        $view = $this->view($paginatedCollection);

        return $this->handleView($view);
    }

    /**
     * Get unseen notification count.
     * @return Response
     * @Route(
     *     "/count.{_format}",
     *     name="api_show_notification_count",
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get unseen notification count.",
     *     views = { "default" },
     *     section="Notifications"
     * )
     *
     * */
    public function countAction()
    {
        /** @var \AppBundle\Entity\User $currentUser */
        $currentUser = $this->get('app.tools')->getCurrentUser();

        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        $data = $entityManager->getRepository('AppBundle:Notification')
            ->findAllQueryBuilder()->andWhere('n.to = :currentUser')
            ->andWhere('n.seen = :seen')
            ->setParameter('currentUser', $currentUser)
            ->setParameter('seen', false)
            ->getQuery()
            ->getResult()
        ;

        $view = $this->view(['count' => count($data)]);

        return $this->handleView($view);
    }

    /**
     * Get notification setup.
     * @return Response
     * @Route(
     *     "/setup.{_format}",
     *     name="api_show_notification_setup",
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get notification setup.",
     *     views = { "default" },
     *     section="Notifications"
     * )
     *
     * */
    public function getSetupAction()
    {
        $data = [];

        $user = $this->get('app.tools')->getCurrentUser();
        $entityManager = $this->getDoctrine()->getManager();

        $channels = $entityManager->getRepository('AppBundle:NotificationChannel')
            ->findAll();

        $types = $entityManager->getRepository('AppBundle:NotificationType')
            ->findAll();

        foreach ($channels as $channel) {
            foreach ($types as $type) {
                $setup = $entityManager->getRepository('AppBundle:NotificationPreferences')
                    ->findOneBy([
                        'notificationChannel' => $channel,
                        'notificationType' => $type,
                        'userPreferences' => $user->getPreferences()
                    ]);

                $data['channel'][$channel->getId()]['name'] = $channel->getName();
                $data['channel'][$channel->getId()]['type']['name'] = $type->getName();
                $data['channel'][$channel->getId()]['type']['collection'][$type->getId()] = boolval($setup);
            }
        }

        $view = $this->view($data);

        return $this->handleView($view);
    }

    /**
     * Get notification.
     * @param Notification $notification
     * @return Response
     * @Route(
     *     "/{id}.{_format}",
     *     name="api_notification_show",
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get notification.",
     *     views = { "default" },
     *     section="Notifications"
     * )
     *
     * */
    public function showAction(Notification $notification)
    {
        $this->denyAccessUnlessGranted(CheckOwnershipVoter::OWNER, $notification);
        $view = $this->view($notification);

        return $this->handleView($view);
    }

    /**
     * Create notification.
     * @param Request $request
     * @return mixed
     * @Route(
     *     ".{_format}",
     *     name="api_notification_new",
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Create notification.",
     *     views = { "default" },
     *     section="Notifications"
     * )
     *
     * */
    public function newAction(Request $request)
    {
        /** @var \AppBundle\Entity\User $currentUser */
        $currentUser = $this->get('app.tools')->getCurrentUser();

        /** @var Notification $notification */
        $notification = new Notification();
        $notification->setSeen(false);
        $notification->setChecked(false);
        $notification->setFrom($currentUser);

        $form = $this->createForm(NotificationType::class, $notification, [ 'csrf_protection' => false ]);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($notification);
            $entityManager->flush();

            $view = $this->view($notification, Response::HTTP_CREATED);
            return $this->handleView($view);
        }

        $statusCode = Response::HTTP_BAD_REQUEST;
        $view = $this->view(['type' => 'error', 'message' => 'Invalid form', 'recordset' => $form->getErrors(), 'code' => $statusCode], $statusCode);
        return $this->handleView($view);
    }

    /**
     * Delete notification.
     * @param Notification $notification
     * @return Response
     * @Route(
     *     "/{id}.{_format}",
     *     name="api_notification_delete",
     *     defaults={"_format": "json"},
     *     methods={"DELETE"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Delete notification.",
     *     views = { "default" },
     *     section="Notifications"
     * )
     *
     * */
    public function deleteAction(Notification $notification)
    {
        $this->denyAccessUnlessGranted(CheckOwnershipVoter::OWNER, $notification);

        /** @var User $user */
        $user = $this->getUser();

        if ($user->getUsername() != $notification->getTo()->getUsername()) {
            throw new AccessDeniedHttpException();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($notification);

        try {
            $entityManager->flush();
            return new Response(null, 204);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ? $e->getCode() : 500;

            $output = [
                'type' => 'error',
                'code' => $e->getCode() ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
                'recordset' => []
            ];

            $view = $this->view($output, $statusCode);

            return $this->handleView($view);
        }
    }
}
