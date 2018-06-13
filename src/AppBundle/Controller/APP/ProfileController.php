<?php

namespace AppBundle\Controller\APP;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Profile Controller.
 * Note: This actions extends from FOSUserBundle
 * @Route("/profile")
 * */
class ProfileController extends Controller
{
    /**
     * Show user profile.
     * @param User $user
     * @return Response
     * @Route("/user/{usernameCanonical}", name="app_user_profile")
     * */
    public function userProfileAction(User $user)
    {
        return $this->render('@FOSUser/Profile/show.html.twig', ['user' => $user]);
    }
}
