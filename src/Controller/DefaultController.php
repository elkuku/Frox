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
    public function index(
        WaypointRepository $repository,
        ProvinceRepository $provinceRepository
    ): Response {
        $provinces = $provinceRepository->findAll();

        return $this->render(
            'default/index.html.twig',
            [
                'numWayPoints' => \count($repository->findAll()),
                'provinces'    => $provinces,
            ]
        );
    }
}
