<?php

namespace AppBundle\Controller\API;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/api")
 * */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="api_index")
     * */
    public function indexAction()
    {
        $output = [];

        return new JsonResponse($output, 200);
    }
}
