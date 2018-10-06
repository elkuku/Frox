<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Province;
use App\Entity\Waypoint;
use App\Form\ImportFormType;
use App\Form\WaypointFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends AbstractController
{
    /**
     * @Route("/import", name="import")
     */
    public function index(Request $request)
    {
        $form = $this->createForm(ImportFormType::class);
        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($waypoint);
//            $em->flush();

//            echo $dat
            if ($data['gpxRaw']) {
                $points = $this->importGpx($data['gpxRaw'], $data['province'], $data['city']);

                if ($points) {
                    $this->addFlash('success', $points.' Waypoint(s) imported!');

                } else {
                    $this->addFlash('warning', 'No Waypoints imported!');

                }
            }

            return $this->redirectToRoute('default');
        }

        return $this->render(
            'import/index.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/import_run", name="run-import")
     */
    public function import(Request $request)
    {
        $intelLink = $request->request->get('intelLink');
        $gpxFile   = $request->request->get('gpxFile');

        var_dump($intelLink);
        var_dump($gpxFile);

        if ($gpxFile) {

            $points = $this->importGpx($gpxFile);

            var_dump($points);
        }

        return $this->render(
            'import/index.html.twig',
            [
                'controller_name' => 'ImportController',
            ]
        );
    }

    private function importGpx(string $gpxData, ?Province $province = null, string $city = '')
    {
        $repository    = $this->getDoctrine()
            ->getRepository(Waypoint::class);
        $entityManager = $this->getDoctrine()->getManager();
        $category      = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['id' => 1]);

        $xml = simplexml_load_string($gpxData);

        $cnt = 0;

        foreach ($xml->children() as $wp) {
            $w = $repository->findOneBy(
                [
                    'lat' => $wp['lat'],
                    'lon' => $wp['lon'],
                ]
            );

            if (!$w) {
                $wayPoint = new Waypoint();

                $wayPoint->setName($wp->name);
                $wayPoint->setLat((float)$wp['lat']);
                $wayPoint->setLon((float)$wp['lon']);
                $wayPoint->setCategory($category);
                $wayPoint->setProvince($province);
                $wayPoint->setCity($city);

                $entityManager->persist($wayPoint);

                $entityManager->flush();

                $cnt++;
            }
        }

        return $cnt;
    }
}
