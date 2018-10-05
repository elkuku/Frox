<?php

namespace App\Controller;

use App\Entity\Waypoint;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController
{
    /**
     * @Route("/export", name="export")
     */
    public function index()
    {
        $waypoints = $this->getDoctrine()
            ->getRepository(Waypoint::class)
            ->findAll();

        return $this->render(
            'export/index.html.twig',
            [
                'controller_name' => 'ExportController',
                'waypoints'       => $waypoints,
            ]
        );
    }

    /**
     * @Route("/export_run", name="run-export")
     */
    public function export(Request $request)
    {
        $points = $request->request->get('points');

        $waypoints = $this->getDoctrine()
            ->getRepository(Waypoint::class)
            ->findBy(['id' => $points]);

        $gpx      = [];
        $maxField = [];

        foreach ($waypoints as $waypoint) {
            $gpx[]      = $waypoint->getName();

            $ps         = $waypoint->getLat().','.$waypoint->getLon();
            $maxField[] = $waypoint->getName().';https://www.ingress.com/intel?ll='.$ps.'&z=17&pll='.$ps;
        }

        return $this->render(
            'export/result.html.twig',
            [
                'gpx' => implode("\n", $gpx),
                'maxField' => implode("\n", $maxField),
            ]
        );
    }
}
