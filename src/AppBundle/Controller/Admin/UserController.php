<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Security\PrimaryVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin User Controller.
 * @Route("/administrator/user")
 * */
class UserController extends Controller
{
    /**
     * Show user list.
     * @return Response
     * @Route("/", name="administrator_user_list")
     * */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted(PrimaryVoter::INDEX, User::class);

        /** @var User $current_user */
        $current_user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $opts = [];

        if ($current_user->hasRole(User::ROLE_SUPER_ADMIN)) {
            $users = $entityManager->getRepository('AppBundle:User')
                ->findAll();
        } else {
            $users = $entityManager->getRepository('AppBundle:User')
                ->findBy(['company' => $current_user->getCompany()]);
        }

        $opts['users'] = $users;

        return $this->render('Admin/User/index.html.twig', $opts);
    }

    /**
     * Create new user entity.
     * @param Request $request
     * @return Response
     * @Route("/new", name="administrator_user_new")
     * */
    public function newAction(Request $request)
    {
        $user = new User();
        $user->setEnabled(true);

        $this->denyAccessUnlessGranted(PrimaryVoter::CREATE, $user);

        $form = $this->createForm('AppBundle\Form\Admin\UserType', $user, ['current_user' => $this->getUser(), 'validation_groups' => ['create']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPlainPassword($user->passcode);

            $this->joinCompany($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $translator = $this->get('translator');
            $this->addFlash('success', $translator->trans('app.user.new.success', [], 'AppBundle'));

            return $this->redirectToRoute('administrator_user_list');
        }

        return $this->render('Admin/User/new.html.twig', ['user' => $user, 'form' => $form->createView()]);
    }

    /**
     * Edit user entity
     * @param Request $request
     * @param User $user User entity
     * @return Response
     * @Route("/{id}/edit", name="administrator_user_edit")
     * */
    public function editAction(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted(PrimaryVoter::UPDATE, $user);

        $form = $this->createForm('AppBundle\Form\Admin\UserType', $user, ['current_user' => $this->getUser(), 'validation_groups' => ['edit']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPlainPassword($user->passcode);

            $this->joinCompany($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $translator = $this->get('translator');
            $this->addFlash('success', $translator->trans('app.user.edit.success', [], 'AppBundle'));

            return $this->redirectToRoute('administrator_user_list');
        }

        return $this->render('Admin/User/edit.html.twig', ['user' => $user, 'form' => $form->createView()]);
    }

    /**
     * Join new user to Admin Company.
     * @param User $user
     *
     * */
    private function joinCompany(User &$user)
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN') == false && $this->isGranted('ROLE_ADMIN') == true) {
            /** @var User $current_user */
            $current_user = $this->getUser();
            $company = $current_user->getCompany();

            if ($company) {
                $user->setCompany($company);
            }
        }
    }

    /**
     * Remove user entity.
     * @param User $user User entity
     * @return Response
     * @Route("/{id}/delete", name="administrator_user_delete", methods={"DELETE"})
     * */
    public function deleteAction(User $user)
    {
        $this->denyAccessUnlessGranted(PrimaryVoter::DELETE, $user);

        $translator = $this->get('translator');
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($user);

        try {
            $this->addFlash('success', $translator->trans('app.user.remove.success', [], 'AppBundle'));
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('danger', $translator->trans('app.user.remove.error', [], 'AppBundle'));
        }

        return $this->redirectToRoute('administrator_user_list');
    }
}
