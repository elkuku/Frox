<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/map")
 */
class MapController extends AbstractController
{
    /**
     * @Route("/maxfield", name="map-maxfield")
     */
    public function map(): Response
    {
        return $this->render('maps/maxfield.html.twig');
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
