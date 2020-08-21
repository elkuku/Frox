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
    public function new(Request $request): Response
    {
        $collection = new Collection();
        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
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
     * @Route("/{id}", name="collection_show", methods={"GET"})
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
     * @Route("/{id}/edit", name="collection_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Collection $collection): Response
    {
        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

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
    public function delete(Request $request, Collection $collection): Response
    {
        if ($this->isCsrfTokenValid(
            'delete'.$collection->getId(),
            $request->request->get('_token')
        )
        ) {
            $entityManager = $this->getDoctrine()->getManager();
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
}
