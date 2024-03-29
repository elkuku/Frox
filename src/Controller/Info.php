<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Info extends AbstractController
{
    #[Route(path: '/provinces', name: 'provinces')]
    public function provinces(): Response
    {
        return $this->render('info/provinces.html.twig');
    }

    #[Route(path: '/cells', name: 'cells')]
    public function cells(): Response
    {
        return $this->render('info/cells.html.twig');
    }
}
