<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\User;
use AppBundle\Form\API\UserType;
use Doctrine\ORM\EntityManager;
use AppBundle\Security\PrimaryVoter;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 *
 * UserController API Class
 * @Route("/api/{version}/users")
 *
 * */
class UserController extends FOSRestController
{
    /**
     * Get Users list.
     * @param ParamFetcherInterface $paramFetcher
     * @param Request $request
     *
     * @Route(
     *     ".{_format}",
     *     name="api_user_list",
     *     defaults={"_format": "json", "version": "v1"},
     *     methods={"GET"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get user list.",
     *     views = { "default" },
     *     section="Users"
     * )
     *
     * @QueryParam(name="page", requirements="\d+", default=1, nullable=true, description="Page")
     * @QueryParam(name="limit", requirements="\d+", default=10, nullable=true, description="Query limit")
     * @QueryParam(name="sort_field", default="first_name", nullable=true, description="Sort field")
     * @QueryParam(name="sort", default="DESC", nullable=true, description="Sort direction")
     * @QueryParam(name="search", default="", nullable=true, description="Text to filter...")
     *
     * @return Response
     *
     * */
    public function indexAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        $this->denyAccessUnlessGranted(PrimaryVoter::MANAGE, User::class);
        
        $search = $paramFetcher->get('search');
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');
        $sortField = $paramFetcher->get('sort_field');
        $sort = $paramFetcher->get('sort');

        /** API version */
        $version = $request->get('version');

        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        $queryBuilder = $entityManager->getRepository('AppBundle:User')
            ->findAllQueryBuilder($search, $sortField, $sort)
        ;

        $paginatedCollection = $this->get('app.pagination_factory')
            ->createCollection($queryBuilder, $search, $page, $limit, 'api_user_list', [ 'sort_field' => $sortField, 'sort' => $sort, 'version' => $version ]);

        $view = $this->view($paginatedCollection);

        return $this->handleView($view);
    }

    /**
     * Get user.
     * @param User $user
     * @return Response
     * @Route(
     *     "/{id}.{_format}",
     *     name="api_user_show",
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get user.",
     *     views = { "default" },
     *     section="Users"
     * )
     *
     * */
    public function showAction(User $user)
    {
        $this->denyAccessUnlessGranted(PrimaryVoter::SHOW, User::class);
        
        $view = $this->view($user);

        return $this->handleView($view);
    }

    /**
     * Create user.
     * @param Request $request
     * @return mixed
     * @Route(
     *     ".{_format}",
     *     name="api_user_new",
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Create user.",
     *     views = { "default" },
     *     section="Users"
     * )
     *
     * */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted(PrimaryVoter::CREATE, User::class);
        
        $user = new User();
        $user->setEnabled(true);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $user->setPlainPassword($user->passcode);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $view = $this->view($user, Response::HTTP_CREATED);

            return $this->handleView($view);
        }

        $statusCode = Response::HTTP_BAD_REQUEST;
        $view = $this->view(['type' => 'error', 'message' => 'Invalid form', 'recordset' => $form->getErrors(), 'code' => $statusCode], $statusCode);
        return $this->handleView($view);
    }

    /**
     * Update user.
     * @param Request $request
     * @param User $user
     * @return mixed
     * @Route(
     *     "/{id}.{_format}",
     *     name="api_user_edit",
     *     defaults={"_format": "json"},
     *     methods={"PATCH"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Update user.",
     *     views = { "default" },
     *     section="Users"
     * )
     *
     * */
    public function editAction(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted(PrimaryVoter::UPDATE, User::class);
        
        $form = $this->createForm(UserType::class, $user, ['method' => 'PATCH']);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $userManager = $this->get('fos_user.user_manager');

            if ($user->passcode) {
                $user->setPlainPassword($user->passcode);
            }

            $userManager->updateUser($user);
            $view = $this->view($user);

            return $this->handleView($view);
        }

        $statusCode = Response::HTTP_BAD_REQUEST;
        $view = $this->view(['type' => 'error', 'message' => 'Invalid form', 'recordset' => $form->getErrors(), 'code' => $statusCode], $statusCode);
        return $this->handleView($view);
    }

    /**
     * Delete user.
     * @param User $user
     * @return Response
     * @Route(
     *     "/{id}.{_format}",
     *     name="api_user_delete",
     *     defaults={"_format": "json"},
     *     methods={"DELETE"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Delete user.",
     *     views = { "default" },
     *     section="Users"
     * )
     *
     * */
    public function deleteAction(User $user)
    {
        $this->denyAccessUnlessGranted(PrimaryVoter::DELETE, User::class);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);

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
}
