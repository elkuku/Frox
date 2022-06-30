<?php

namespace App\Controller;

use App\Helper\Paginator\PaginatorTrait;
use App\Repository\WaypointRepository;
use App\Service\MaxFieldGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController
{
    use PaginatorTrait;

    #[Route(path: '/export2', name: 'export2')]
    public function export2(
        WaypointRepository $repository,
        MaxFieldGenerator $maxFieldGenerator,
        Request $request
    ): JsonResponse {
        $points = $request->request->all('points');

        if ($points) {
            $wayPoints = $repository->findBy(['id' => $points]);
            $data = [
                'maxfield' => $maxFieldGenerator->convertWayPointsToMaxFields(
                    $wayPoints
                ),
                'gpx'      => '',//$maxFieldGenerator->createGpx($wayPoints),
            ];
        } else {
            $message = 'No WayPoints Selected!';
            $data = ['maxfield' => $message, 'gpx' => $message];
        }

        return $this->json($data);
    }
}
