<?php

namespace App\Controller;

use App\Entity\Waypoint;
use App\Helper\Paginator\PaginatorTrait;
use App\Repository\ProvinceRepository;
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

    /**
     * @Route("/export", name="export")
     */
    public function index(WaypointRepository $repository, ProvinceRepository $provinceRepository, MaxFieldGenerator $maxFieldGenerator, Request $request): Response
    {
        $points    = $request->request->get('points');

        if ($points) {
            $wayPoints = $repository->findBy(['id' => $points]);
            return $this->render(
                'export/result.html.twig',
                [
                    'gpx'      => $this->createGpx($wayPoints),
                    'maxField' => $maxFieldGenerator->convertWayPointsToMaxFields($wayPoints),
                ]
            );
        }

        $paginatorOptions = $this->getPaginatorOptions($request);

        $waypoints = $repository->getRawList($paginatorOptions);

        $paginatorOptions->setMaxPages((int)ceil($waypoints->count() / $paginatorOptions->getLimit()));

        return $this->render(
            'export/index.html.twig',
            [
                'waypoints'        => $waypoints,
                'waypoints_cnt'    => $waypoints->count(),
                'provinces'        => $provinceRepository->findAll(),
                'paginatorOptions' => $paginatorOptions,
            ]
        );
    }

    /**
     * @Route("/export2", name="export2")
     */
    public function export2(WaypointRepository $repository, MaxFieldGenerator $maxFieldGenerator, Request $request): JsonResponse
    {
        $points = $request->request->get('points');

        if ($points) {
            $wayPoints = $repository->findBy(['id' => $points]);
            $data = [
                'maxfield' => $maxFieldGenerator->convertWayPointsToMaxFields($wayPoints),
                'gpx' => $this->createGpx($wayPoints)
            ];
        } else {
            $message = 'No WayPoints Selected!';
            $data    = ['maxfield' => $message, 'gpx' => $message];
        }

        return $this->json($data);
    }


    /**
     * @param Waypoint[] $wayPoints
     */
    private function createGpx(array $wayPoints): string
    {
        $xml = [];

        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<gpx version="1.0" creator="GPSBabel - http://www.gpsbabel.org" xmlns="http://www.topografix.com/GPX/1/0">';

        foreach ($wayPoints as $wayPoint) {
            $xml[] = '<wpt lat="'.$wayPoint->getLat().'" lon="'.$wayPoint->getLon().'">';
            $xml[] = '  <name>' . $wayPoint->getName().'</name>';
            //     $xml[] = '  <cmt>'.$names[$i].'</cmt>';
            //     $xml[] = '  <desc>'.$names[$i].'</desc>';
            $xml[] = '</wpt>';
        }

        $xml[] = '</gpx>';

        return implode("\n", $xml);
    }
}
