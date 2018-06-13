<?php

namespace AppBundle\Controller\SuperAdmin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/administrator")
 * */
class DashboardController extends Controller
{
    /**
     * Administrator Dashboard Controller.
     * @Route("/", name="super_administrator_dashboard")
     * */
    public function indexAction()
    {
        return $this->render('SuperAdmin/Dashboard/index.html.twig');
    }
}
