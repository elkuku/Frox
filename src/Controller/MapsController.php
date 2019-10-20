<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/maps")
 */
class MapsController extends AbstractController
{
    /**
     * @Route("/maxfield", name="map-maxfield")
     */
    public function map(): Response
    {
        return $this->render('maps/maxfield.html.twig');
    }
}
