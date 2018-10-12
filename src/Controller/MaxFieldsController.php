<?php

namespace App\Controller;

use App\Repository\WaypointRepository;
use App\Service\MaxFieldGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MaxFieldsController extends AbstractController
{
    /**
     * @Route("/max-fields", name="max_fields")
     */
    public function index(MaxFieldGenerator $maxFieldGenerator): Response
    {
        return $this->render('max_fields/index.html.twig', [
            'controller_name' => 'MaxFieldsController',
            'list' => $maxFieldGenerator->getList(),
        ]);
    }

    /**
     * @Route("/max-fields/{item}", name="max_fields_result")
     */
    public function display(MaxFieldGenerator $maxFieldGenerator, string $item): Response
    {
        return $this->render('max_fields/result.html.twig', [
            'item' => $item,
            'info' => $maxFieldGenerator->getInfo($item),
            'list' => $maxFieldGenerator->getContentList($item),
        ]);
    }

    /**
     * @Route("/export_maxfields", name="export-maxfields")
     */
    public function generateMaxFields(WaypointRepository $repository, MaxFieldGenerator $maxFieldGenerator, Request $request): Response
    {
        $points = $request->request->get('points');

        if (!$points) {
            throw new NotFoundHttpException('No waypoints selected.');
        }

        $wayPoints = $repository->findBy(['id' => $points]);
        $maxField  = $maxFieldGenerator->convertWayPointsToMaxFields($wayPoints);
        $buildName = $request->request->get('buildName');
        $playersNum = (int)$request->request->get('players_num')?:1;
        $timeStamp = date('Y-m-d');
        $projectName = $playersNum.'pl-'.$timeStamp.'-'.$buildName;

        $maxFieldGenerator->generate($projectName, $maxField, $playersNum);

        return $this->render(
            'max_fields/result.html.twig',
            [
                'item' => $projectName,
                'info' => $maxFieldGenerator->getInfo($projectName),
                'list' => $maxFieldGenerator->getContentList($projectName),

            ]
        );
    }

}
