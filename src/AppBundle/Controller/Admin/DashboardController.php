<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/administrator")
 * */
class DashboardController extends Controller
{
    /**
     * Administrator Dashboard Controller.
     * @return Response
     * @Route("/", name="administrator_dashboard")
     * */
    public function indexAction()
    {
        return $this->render('Admin/Dashboard/index.html.twig');
    }
}
