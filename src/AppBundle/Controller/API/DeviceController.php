<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\User;
use AppBundle\Entity\Device;
use AppBundle\Entity\Company;
use Doctrine\ORM\EntityManager;
use AppBundle\Form\API\DeviceType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 *
 * DeviceController API Class
 * @Route("/api/{version}/users/{username}/devices")
 *
 * */
class DeviceController extends FOSRestController
{
    /**
     * Get Devices list.
     * @param ParamFetcherInterface $paramFetcher
     * @param Request $request
     * @param User $user
     *
     * @Route(
     *     ".{_format}",
     *     name="api_device_list",
     *     defaults={"_format": "json", "version": "v1"},
     *     methods={"GET"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get device list.",
     *     views = { "default" },
     *     section="Devices"
     * )
     *
     * @QueryParam(name="limit", requirements="\d+", default=10, nullable=true, description="Query limit")
     * @QueryParam(name="page", requirements="\d+", default=1, nullable=true, description="Page")
     * @QueryParam(name="sort_field", default="uuid", nullable=true, description="Sort field")
     * @QueryParam(name="sort", default="DESC", nullable=true, description="Sort direction")
     * @QueryParam(name="search", default="", nullable=true, description="Text to filter...")
     *
     * @ParamConverter("user", class="AppBundle:User", options={"username" = "username"})
     *
     * @return Response
     *
     * */
    public function indexAction(ParamFetcherInterface $paramFetcher, Request $request, User $user)
    {
        $search = $paramFetcher->get('search');
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');
        $sortField = $paramFetcher->get('sort_field');
        $sort = $paramFetcher->get('sort');

        /** API version */
        $version = $request->get('version');

        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        /** @var Company $company */
        $company = $this->get('app.tools')->getCurrentCompany();

        $queryBuilder = $entityManager->getRepository('AppBundle:Device')
            ->findAllQueryBuilder($search, $sortField, $sort)
            ->andWhere('d.user = :user')
            ->andWhere('u.company = :company')
            ->setParameter('company', $company)
            ->setParameter('user', $user)
        ;

        $paginatedCollection = $this->get('app.pagination_factory')
            ->createCollection($queryBuilder, $search, $page, $limit, 'api_device_list', [ 'username' => $user->getUsername(), 'sort_field' => $sortField, 'sort' => $sort, 'version' => $version ]);

        $view = $this->view($paginatedCollection);

        return $this->handleView($view);
    }

    /**
     * Get device.
     * @param Device $device
     * @param User $user
     *
     * @Route(
     *     "/{id}.{_format}",
     *     name="api_device_show",
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get device.",
     *     views = { "default" },
     *     section="Devices"
     * )
     *
     * @ParamConverter("user", class="AppBundle:User", options={"username" = "username"})
     *
     *
     * @return Response
     * */
    public function showAction(Device $device, User $user)
    {
        if ($device->getUser() !== $user) {
            $view = $this->view([], Response::HTTP_NOT_FOUND);

            return $this->handleView($view);
        }

        $view = $this->view($device);

        return $this->handleView($view);
    }

    /**
     * Create device.
     * @param Request $request
     * @param User $user
     *
     * @Route(
     *     ".{_format}",
     *     name="api_device_new",
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Create device.",
     *     views = { "default" },
     *     section="Devices"
     * )
     *
     * @ParamConverter("user", class="AppBundle:User", options={"username" = "username"})
     *
     *
     * @return Response
     *
     * */
    public function newAction(Request $request, User $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $uuid = $request->get('uuid');

        $exists = $entityManager->getRepository('AppBundle:Device')
            ->findOneBy(['uuid' => $uuid]);

        if ($exists) {
            $device = $exists;
            $device->setUser($user);
            $entityManager->flush();
            $view = $this->view($device, Response::HTTP_CREATED);

            return $this->handleView($view);
        }

        /** @var Device $device */
        $device = new Device();
        $device->setUser($user);

        $form = $this->createForm(DeviceType::class, $device, [ 'csrf_protection' => false ]);
        $form->handleRequest($request);

        if (!$exists) {

            if ($form->isValid()) {
                $entityManager->persist($device);
                $entityManager->flush();

                $view = $this->view($device, Response::HTTP_CREATED);
                return $this->handleView($view);
            }
        }

        $statusCode = Response::HTTP_BAD_REQUEST;
        $view = $this->view(['type' => 'error', 'message' => 'Invalid form', 'recordset' => $form->getErrors(), 'code' => $statusCode], $statusCode);
        return $this->handleView($view);
    }

    /**
     * Update device.
     * @param Request $request
     * @param Device $device
     * @param User $user
     *
     * @Route(
     *     "/{id}.{_format}",
     *     name="api_device_edit",
     *     defaults={"_format": "json"},
     *     methods={"PUT"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Update device.",
     *     views = { "default" },
     *     section="Devices"
     * )
     *
     * @ParamConverter("user", class="AppBundle:User", options={"username" = "username"})
     *
     *
     * @return Response
     *
     * */
    public function editAction(Request $request, Device $device, User $user)
    {
        $device->setUser($user);
        $form = $this->createForm(DeviceType::class, $device, ['method' => 'PATCH', 'csrf_protection' => false ]);
        $form->submit($request->request->all(), false);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $view = $this->view($device, Response::HTTP_OK);
            return $this->handleView($view);
        }

        $statusCode = Response::HTTP_BAD_REQUEST;
        $view = $this->view(['type' => 'error', 'message' => 'Invalid form', 'recordset' => $form->getErrors(), 'code' => $statusCode], $statusCode);
        return $this->handleView($view);
    }

    /**
     * Delete device.
     * @param Device $device
     * @param User $user
     *
     * @Route(
     *     "/{id}.{_format}",
     *     name="api_device_delete",
     *     defaults={"_format": "json"},
     *     methods={"DELETE"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Delete device.",
     *     views = { "default" },
     *     section="Devices"
     * )
     *
     * @ParamConverter("user", class="AppBundle:User", options={"username" = "username"})
     *
     *
     * @return Response
     *
     * */
    public function deleteAction(Device $device, User $user)
    {
        if ($device->getUser() !== $user) {
            $view = $this->view([], Response::HTTP_NOT_FOUND);

            return $this->handleView($view);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($device);

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
