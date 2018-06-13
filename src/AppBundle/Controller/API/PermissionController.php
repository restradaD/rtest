<?php

namespace AppBundle\Controller\API;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Permission;
use AppBundle\Entity\RolePermission;
use AppBundle\Security\PrimaryVoter;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Form\API\PermissionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 *
 * PermissionController API Class
 * @Route("/api/{version}/permissions")
 *
 * */
class PermissionController extends FOSRestController
{
    /**
     * Get Permissions list.
     * @param ParamFetcherInterface $paramFetcher
     * @param Request $request
     *
     * @Route(
     *     ".{_format}",
     *     name="api_permission_list",
     *     defaults={"_format": "json", "version": "v1"},
     *     methods={"GET"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get permission list.",
     *     views = { "default" },
     *     section="Permissions"
     * )
     *
     * @QueryParam(name="limit", requirements="\d+", default=10, nullable=true, description="Query limit")
     * @QueryParam(name="page", requirements="\d+", default=1, nullable=true, description="Page")
     * @QueryParam(name="sort_field", default="code", nullable=true, description="Sort field")
     * @QueryParam(name="sort", default="DESC", nullable=true, description="Sort direction")
     * @QueryParam(name="search", default="", nullable=true, description="Text to filter...")
     *
     * @return Response
     *
     * */
    public function indexAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        $this->denyAccessUnlessGranted(PrimaryVoter::MANAGE, Permission::class);

        $search = $paramFetcher->get('search');
        $limit = $paramFetcher->get('limit');
        $page = $paramFetcher->get('page');
        $sortField = $paramFetcher->get('sort_field');
        $sort = $paramFetcher->get('sort');

        /** API version */
        $version = $request->get('version');

        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        $queryBuilder = $entityManager->getRepository('AppBundle:Permission')
            ->findAllQueryBuilder($search, $sortField, $sort)
        ;

        $paginatedCollection = $this->get('app.pagination_factory')
            ->createCollection($queryBuilder, $search, $page, $limit, 'api_permission_list', [ 'sort_field' => $sortField, 'sort' => $sort, 'version' => $version ]);

        $view = $this->view($paginatedCollection);

        return $this->handleView($view);
    }

    /**
     * Get permission.
     * @param Permission $permission
     * @return Response
     * @Route(
     *     "/{id}.{_format}",
     *     name="api_permission_show",
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get permission.",
     *     views = { "default" },
     *     section="Permissions"
     * )
     *
     * */
    public function showAction(Permission $permission)
    {
        $this->denyAccessUnlessGranted(PrimaryVoter::SHOW, Permission::class);

        $view = $this->view($permission);

        return $this->handleView($view);
    }

    /**
     * Create permission.
     * @param Request $request
     * @return mixed
     * @Route(
     *     ".{_format}",
     *     name="api_permission_new",
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Create permission.",
     *     views = { "default" },
     *     section="Permissions"
     * )
     *
     * */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted(PrimaryVoter::CREATE, Permission::class);

        $permission = new Permission();

        $form = $this->createForm(PermissionType::class, $permission);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($permission);
            $entityManager->flush();

            return $this->redirectView($this->generateUrl('api_permission_list'), Response::HTTP_CREATED);
        }

        $statusCode = Response::HTTP_BAD_REQUEST;
        $view = $this->view(['type' => 'error', 'message' => 'Invalid form', 'recordset' => $form->getErrors(), 'code' => $statusCode], $statusCode);
        return $this->handleView($view);
    }

    /**
     * Update permission.
     * @param Request $request
     * @param Permission $permission
     * @return mixed
     * @Route(
     *     "/{id}.{_format}",
     *     name="api_permission_edit",
     *     defaults={"_format": "json"},
     *     methods={"PATCH"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Update permission.",
     *     views = { "default" },
     *     section="Permissions"
     * )
     *
     * */
    public function editAction(Request $request, Permission $permission)
    {
        $this->denyAccessUnlessGranted(PrimaryVoter::UPDATE, Permission::class);

        $form = $this->createForm(PermissionType::class, $permission, ['method' => 'PATCH']);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($permission);
            $entityManager->flush();

            return $this->redirectView($this->generateUrl('api_permission_list'), Response::HTTP_OK);
        }

        $statusCode = Response::HTTP_BAD_REQUEST;
        $view = $this->view(['type' => 'error', 'message' => 'Invalid form', 'recordset' => $form->getErrors(), 'code' => $statusCode], $statusCode);
        return $this->handleView($view);
    }

    /**
     * Delete permission.
     * @param Permission $permission
     * @return Response
     * @Route(
     *     "/{id}.{_format}",
     *     name="api_permission_delete",
     *     defaults={"_format": "json"},
     *     methods={"DELETE"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Delete permission.",
     *     views = { "default" },
     *     section="Permissions"
     * )
     *
     * */
    public function deleteAction(Permission $permission)
    {
        $this->denyAccessUnlessGranted(PrimaryVoter::DELETE, Permission::class);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($permission);

        try {
            $entityManager->flush();
            return new Response(null, 204);
        } catch (\Exception $e) {
            $output = [
                'type' => 'error',
                'code' => $e->getCode() ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
                'recordset' => []
            ];

            $view = $this->view($output);

            return $this->handleView($view);
        }
    }

    /**
     * Get Permissions assigned to role.
     * @param Permission $permission
     * @param Request $request
     * @return Response
     * @Route(
     *     "/{id}/role/{role}.{_format}",
     *     name="api_permission_assign_role_get",
     *     defaults={"_format": "json", "version": "v1"},
     *     methods={"GET"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get Permissions assigned to role",
     *     views = { "default" },
     *     section="Permissions"
     * )
     *
     * */
    public function getAssignPermissionsAction(Permission $permission, Request $request)
    {
        $this->denyAccessUnlessGranted(PrimaryVoter::ASSIGN, Permission::class);
        
        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();
        $role = $request->get('role');

        $data = $entityManager->getRepository('AppBundle:RolePermission')
            ->findOneBy(['permission' => $permission, 'role' => $role]);

        if (!$data) {
            throw $this->createNotFoundException('Permission not found!');
        }

        $view = $this->view($data);

        return $this->handleView($view);
    }

    /**
     * Assign Permissions to role.
     * @param Permission $permission
     * @param Request $request
     * @return Response
     * @Route(
     *     "/{id}/role/{role}.{_format}",
     *     name="api_permission_assign_role_post",
     *     defaults={"_format": "json", "version": "v1"},
     *     methods={"POST"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Assign permission to role.",
     *     views = { "default" },
     *     section="Permissions"
     * )
     *
     * */
    public function postAssignPermissionsAction(Permission $permission, Request $request)
    {
        $this->denyAccessUnlessGranted(PrimaryVoter::ASSIGN, Permission::class);
        
        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();
        $role = $request->get('role');

        $data = $entityManager->getRepository('AppBundle:RolePermission')
            ->findOneBy(['permission' => $permission, 'role' => $role]);

        if (!$data) {
            $assignment = new RolePermission();
            $assignment->setPermission($permission);
            $assignment->setRole($role);

            $entityManager->persist($assignment);
            $entityManager->flush();

            $data = $assignment;
        }

        $view = $this->view($data);

        return $this->handleView($view);
    }

    /**
     * Remove assigned permissions.
     * @param Permission $permission
     * @param Request $request
     * @return Response
     * @Route(
     *     "/{id}/role/{role}.{_format}",
     *     name="api_permission_assign_role_delete",
     *     defaults={"_format": "json", "version": "v1"},
     *     methods={"DELETE"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Remove assigned permissions..",
     *     views = { "default" },
     *     section="Permissions"
     * )
     *
     * */
    public function deleteAssignPermissionsAction(Permission $permission, Request $request)
    {
        $this->denyAccessUnlessGranted(PrimaryVoter::ASSIGN, Permission::class);
        
        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();
        $role = $request->get('role');

        $data = $entityManager->getRepository('AppBundle:RolePermission')
            ->findOneBy(['permission' => $permission, 'role' => $role]);

        if (!$data) {
            throw $this->createNotFoundException('Permission not found!');
        }

        $entityManager->remove($data);
        $entityManager->flush();

        $data = [];
        $view = $this->view($data, 204);

        return $this->handleView($view);
    }
}
