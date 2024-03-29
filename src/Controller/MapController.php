<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/map')]
class MapController extends AbstractController
{
    #[Route(path: '/maxfield', name: 'map-maxfield')]
    public function maxfield(): Response
    {
        return $this->render('maps/maxfield.html.twig');
    }

    #[Route(path: '/edit', name: 'map-edit')]
    public function edit(): Response
    {
        return $this->render('maps/edit.html.twig');
    }
}
