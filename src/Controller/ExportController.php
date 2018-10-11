<?php

namespace App\Controller;

use App\Entity\Waypoint;
use App\Helper\Paginator\PaginatorTrait;
use App\Repository\ProvinceRepository;
use App\Repository\WaypointRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController
{
    use PaginatorTrait;

    /**
     * @Route("/export", name="export")
     */
    public function index(WaypointRepository $repository, ProvinceRepository $provinceRepository, Request $request)
    {
        $points = $request->request->get('points');

        if ($points) {
            return $this->export($repository->findBy(['id' => $points]));
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
    public function export2(WaypointRepository $repository, Request $request)
    {
        $points = $request->request->get('points');

        if ($points) {
            $waypoints = $repository->findBy(['id' => $points]);
            $maxField  = [];
            $gpx = [];

            foreach ($waypoints as $waypoint) {
                $maxField[] = $this->getMaxFieldsLink($waypoint);
                $gpx[] = $waypoint->getName();
            }
            $data = ['maxfield' => implode("\n", $maxField), 'gpx' => implode("\n", $gpx)];
        } else {
            $message = 'No WayPoints Selected!';
            $data = ['maxfield' => $message, 'gpx' => $message];
        }

        return $this->json($data);
    }

    /**
     * //     * xRoute("/export_run", name="run-export")
     */
    private function export(array $waypoints)
    {
        $gpx      = [];
        $maxField = [];

        foreach ($waypoints as $waypoint) {
            $gpx[] = $waypoint->getName();

            $ps         = $waypoint->getLat().','.$waypoint->getLon();
            $maxField[] = $waypoint->getName().';https://'.getenv('INTEL_URL').'?ll='.$ps.'&z=17&pll='.$ps;
        }

        return $this->render(
            'export/result.html.twig',
            [
                'gpx'      => implode("\n", $gpx),
                'maxField' => implode("\n", $maxField),
            ]
        );
    }

    private function getMaxFieldsLink(Waypoint $waypoint)
    {
        $points = $waypoint->getLat().','.$waypoint->getLon();

        return $waypoint->getName().';https://'.getenv('INTEL_URL').'?ll='.$points.'&z=17&pll='.$points;
    }
}
