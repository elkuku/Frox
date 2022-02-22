<?php

namespace App\Controller;

use App\Entity\Collection;
use App\Form\CollectionType;
use App\Repository\CollectionRepository;
use App\Repository\WaypointRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/collection")
 */
class CollectionController extends AbstractController
{
    /**
     * @Route("/", name="collection_index", methods={"GET"})
     */
    public function index(CollectionRepository $collectionRepository): Response
    {
        return $this->render(
            'collection/index.html.twig',
            [
                'collections' => $collectionRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="collection_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $collection = new Collection();
        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($collection);
            $entityManager->flush();

            return $this->redirectToRoute('collection_index');
        }

        return $this->render(
            'collection/new.html.twig',
            [
                'collection' => $collection,
                'form'       => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="collection_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(
        Collection $collection,
        WaypointRepository $waypointRepository
    ): Response {
        $points = $waypointRepository->findByIds(
            explode(',', $collection->getPoints())
        );

        $categories = [];

        foreach ($points as $point) {
            $category = $point->getCategory();

            if ($category) {
                $categories[$category->getName()][] = $point;
            } else {
                $categories['None'][] = $point;
            }
        }

        return $this->render(
            'collection/show.html.twig',
            [
                'collection' => $collection,
                'points'     => $points,
                'categories' => $categories,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="collection_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(
        Request $request,
        Collection $collection,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('collection_index');
        }

        return $this->render(
            'collection/edit.html.twig',
            [
                'collection' => $collection,
                'form'       => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="collection_delete", methods={"DELETE"})
     */
    public function delete(
        Request $request,
        Collection $collection,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete'.$collection->getId(),
            $request->request->get('_token')
        )
        ) {
            $entityManager->remove($collection);
            $entityManager->flush();
        }

        return $this->redirectToRoute('collection_index');
    }

    /**
     * @Route("/create", name="collection_create", methods={"POST"})
     */
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $waypoints = $request->request->get('points');
        $name = $request->request->get('name');

        $collection = (new Collection())
            ->setType('gallery')
            ->setPoints(implode(',', $waypoints))
            ->setName($name);

        $entityManager->persist($collection);
        $entityManager->flush();

        $response = ['ok', $name];

        return $this->json($response);
    }

    /**
     * @Route("/d3c0de", name="collection_decode_points", methods={"GET"})
     */
    public function decodePoints(
        Request $request,
        WaypointRepository $waypointRepository
    ): Response {
        $ids = explode(',', $request->query->get('ids'));

        return $this->render(
            'collection/d3c0de.html.twig',
            [
                'wayPoints' => $waypointRepository->findDetailsByIds($ids),
            ]
        );
    }
}
