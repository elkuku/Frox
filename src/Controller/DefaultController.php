<?php

namespace App\Controller;

use App\Repository\ProvinceRepository;
use App\Repository\WaypointRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index(WaypointRepository $repository, ProvinceRepository $provinceRepository): Response
    {
        $provinces = $provinceRepository->findAll();

        return $this->render(
            'default/index.html.twig',
            [
                'numWayPoints' => \count($repository->findAll()),
                'provinces' => $provinces,
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
     * @Route("/map2", name="map2")
     */
    public function map2(): Response
    {
        return $this->render('default/map2.html.twig');
    }

    /**
     * @Route("/map3", name="map3")
     */
    public function map3(): Response
    {
        return $this->render('default/map3.html.twig');
    }
}
