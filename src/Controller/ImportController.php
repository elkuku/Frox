<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Waypoint;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends AbstractController
{
    /**
     * @Route("/import", name="import")
     */
    public function index()
    {
        return $this->render('import/index.html.twig', [
            'controller_name' => 'ImportController',
        ]);
    }
    /**
     * @Route("/import_run", name="run-import")
     */
    public function import(Request $request)
    {
        $intelLink = $request->request->get('intelLink');
        $gpxFile = $request->request->get('gpxFile');

        var_dump($intelLink);
        var_dump($gpxFile);

        if ($gpxFile) {

            $points = $this->importGpx($gpxFile);

            var_dump($points);
        }

        return $this->render('import/index.html.twig', [
            'controller_name' => 'ImportController',
        ]);
    }

    private function importGpx(string $gpxData)
    {
        $repository = $this->getDoctrine()
            ->getRepository(Waypoint::class);
        $entityManager = $this->getDoctrine()->getManager();
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['id' => 1]);

        $xml = simplexml_load_string($gpxData);

        $cnt = 0;

        foreach($xml->children() as $wp) {
            $w = $repository->findOneBy([
                'lat' => $wp['lat'],
                'lon' => $wp['lon'],
            ]);

            if (!$w) {
                $wayPoint = new Waypoint();

                $wayPoint->setName($wp->name);
                $wayPoint->setLat((float)$wp['lat']);
                $wayPoint->setLon((float)$wp['lon']);
                $wayPoint->setCategory($category);

                $entityManager->persist($wayPoint);

                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();

                $cnt ++;
            }
        }

        return $cnt;
    }
}
