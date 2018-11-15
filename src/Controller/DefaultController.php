<?php

namespace App\Controller;

use App\Repository\WaypointRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index(WaypointRepository $repository): Response
    {
        return $this->render(
            'default/index.html.twig',
            [
                'numWayPoints' => \count($repository->findAll()),
            ]
        );
    }

    /**
     * @Route("/map", name="map")
     */
    public function map(): Response
    {
        return $this->render('default/map.html.twig');
    }

    /**
     * @Route("/provinces", name="provinces")
     */
    public function provinces(): Response
    {
        return $this->render('default/provinces.html.twig');
    }
}
