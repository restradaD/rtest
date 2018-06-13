<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\User;
use AppBundle\Form\API\RegisterType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/api/auth")
 * */
class AuthController extends FOSRestController
{
    /**
     * Login a user.
     *
     * @param Request $request
     *
     * @Route(
     *     "/login.{_format}",
     *     name="api_auth_login",
     *     defaults={"_format": "json", "version": "v1"},
     *     methods={"POST"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Get user list.",
     *     views = { "default" },
     *     section="Auth"
     * )
     *
     * @return Response
     *
     * */
    public function loginAction(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $statusCode = Response::HTTP_FORBIDDEN;
        $output =$this->validateData($username, $password, $statusCode);

        $userManager = $this->get('fos_user.user_manager');
        $factory = $this->get('security.encoder_factory');

        /** @var User $user */
        $user = $userManager->findUserByUsernameOrEmail($username);

        if (!$user) {
            $view = $this->view($output, $statusCode);

            return $this->handleView($view);
        }

        $encoder = $factory->getEncoder($user);

        if ( ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) ) {
            $statusCode = Response::HTTP_OK;
            $output = $user;
        }

        $view = $this->view($output, $statusCode);

        return $this->handleView($view);
    }

    private function validateData($username, $password, $statusCode) {
        if ( empty($username) || empty($password) ) {
            return [
                "type" => "error",
                "message" => "Invalid form",
                "recordset" =>
                    [
                        "form" => [
                            "children" => [
                                'username' => [],
                                'password' => []
                            ]
                        ],
                        "errors" => []
                    ],
                "code" => $statusCode
            ];
        }

        return [];
    }

    /**
     * Create user.
     * @param Request $request
     * @return mixed
     * @Route(
     *     "/register.{_format}",
     *     name="api_auth_register",
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     * @View()
     *
     * @ApiDoc(
     *     description="Create user.",
     *     views = { "default" },
     *     section="Auth"
     * )
     *
     * */
    public function newAction(Request $request)
    {
        $user = new User();
        $user->setEnabled(true);
        /** Change default roles here... */
        $user->setRoles([ User::ROLE_USER, User::ROLE_APP ]);
        $user->setLocale($this->getParameter('locale'));

        $form = $this->createForm(RegisterType::class, $user, [ 'csrf_protection' => false, 'validation_groups' => [ 'create' ] ]);
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
}
