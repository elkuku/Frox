<?php

namespace App\Controller;

use App\Repository\WaypointRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index(WaypointRepository $repository)
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
    public function map()
    {
        return $this->render('default/map.html.twig');
    }
}
