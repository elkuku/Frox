<?php

namespace App\Controller;

use App\Entity\Waypoint;
use App\Form\WaypointFormType;
use App\Helper\Paginator\PaginatorTrait;
use App\Repository\ProvinceRepository;
use App\Repository\WaypointRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WaypointsController extends AbstractController
{
    use PaginatorTrait;

    /**
     * @Route("/waypoints", name="waypoints")
     */
    public function index(
        WaypointRepository $repository,
        ProvinceRepository $provinceRepository,
        Request $request
    ): Response {
        $paginatorOptions = $this->getPaginatorOptions($request);

        $waypoints = $repository->getRawList($paginatorOptions);

        $paginatorOptions->setMaxPages((int)ceil($waypoints->count() / $paginatorOptions->getLimit()));

        return $this->render(
            'waypoints/index.html.twig',
            [
                'waypoints'        => $waypoints,
                'provinces'        => $provinceRepository->findAll(),
                'paginatorOptions' => $paginatorOptions,
            ]
        );
    }

    /**
     * @Route("/waypoint/{id}", name="waypoints_edit")
     */
    public function edit(Request $request, Waypoint $waypoint)
    {
        $form = $this->createForm(WaypointFormType::class, $waypoint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $waypoint = $form->getData();
            $em       = $this->getDoctrine()->getManager();
            $em->persist($waypoint);
            $em->flush();
            $this->addFlash('success', 'Waypoint updated!');

            return $this->redirectToRoute('waypoints');
        }

        return $this->render(
            'waypoints/edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/waypoints_run", name="run-waypoints")
     */
    public function waypoints(): Response
    {
        $waypoints = $this->getDoctrine()
            ->getRepository(Waypoint::class)
            ->findAll();

        return $this->render(
            'waypoints/index.html.twig',
            [
                'waypoints' => $waypoints,
            ]
        );
    }

    /**
     * @Route("/waypoints_map", name="map-waypoints")
     */
    public function map(): JsonResponse
    {
        $waypoints = $this->getDoctrine()
            ->getRepository(Waypoint::class)
            ->findAll();

        $wps = [];

        foreach ($waypoints as $waypoint) {
            $w = [];

            $w['name'] = $waypoint->getName();
            $w['lat']  = $waypoint->getLat();
            $w['lng']  = $waypoint->getLon();
            $w['id']   = $waypoint->getId();

            $wps[] = $w;
        }

        return $this->json($wps);
    }

    /**
     * @Route("/waypoints_info/{id}", name="waypoints-info")
     */
    public function info(Waypoint $waypoint): Response
    {

        $waypoints = $this->getDoctrine()
            ->getRepository(Waypoint::class)
            ->findAll();

        return $this->render(
            'waypoints/info.html.twig',
            [
                'waypoint' => $waypoint,
            ]
        );
    }
}
