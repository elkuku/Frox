<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Province;
use App\Entity\Waypoint;
use App\Form\ImportFormType;
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
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $count = 0;

            if ($data['gpxRaw']) {
                try {
                    $count += $this->importGpx($data['gpxRaw'], $data['province'], $data['city']);
                } catch (\UnexpectedValueException $exception) {
                    $this->addFlash('danger', $exception->getMessage());

                    return $this->render(
                        'import/index.html.twig',
                        [
                            'form' => $form->createView(),
                        ]
                    );
                }
            }

            if ($data['intelLink']) {
                $count += $this->importIntelLink($data['intelLink'], $data['province'], $data['city']);
            }

            if ($count) {
                $this->addFlash('success', $count.' Waypoint(s) imported!');
            } else {
                $this->addFlash('warning', 'No Waypoints imported!');
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

    private function importIntelLink(string $intelLink, ?Province $province = null, ?string $city = '')
    {
        $parts = explode('pls=', $intelLink);

        if (false === isset($parts[1])) {
            throw new \UnexpectedValueException('Invalid intel link');
        }

        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['id' => 1]);

        $pairs = explode('_', $parts[1]);

        $wayPoints = [];
        $ws        = [];

        $wayPoint = new Waypoint();

        $wayPoint->setCategory($category);
        $wayPoint->setProvince($province);
        $wayPoint->setCity($city);

        foreach ($pairs as $pair) {
            $points = explode(',', $pair);
            if (4 === \count($points)) {
                // First pair
                $p = $points[0].','.$points[1];
                if (false === \in_array($p, $ws, false)) {
                    $wayPoint->setLat((float)$points[0]);
                    $wayPoint->setLon((float)$points[1]);
                    $wayPoint->setName((string)(count($ws) + 1));

                    $wayPoints[] = $this->createWayPoint(
                        (float)$points[0], (float)$points[1], (string)(count($ws) + 1), $category, $province, $city
                    );
//                    $wayPoints[] = $wayPoint;
                    $ws[] = $p;
                }

                // Second pair
                $p = $points[2].','.$points[3];
                if (false === \in_array($p, $ws, false)) {
                    $wayPoint->setLat((float)$points[2]);
                    $wayPoint->setLon((float)$points[3]);
                    $wayPoint->setName((string)(count($ws) + 1));

//                    $wayPoints[] = $wayPoint;
                    $wayPoints[] = $this->createWayPoint(
                        (float)$points[2], (float)$points[3], (string)(count($ws) + 1), $category, $province, $city
                    );

                    $ws[] = $p;
                }
            }
        }

        return $this->storeWayPoints($wayPoints);
    }

    private function importGpx(string $gpxData, ?Province $province = null, ?string $city = '')
    {
        $repository    = $this->getDoctrine()
            ->getRepository(Waypoint::class);
        $entityManager = $this->getDoctrine()->getManager();
        $category      = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['id' => 1]);

        try {
            $xml = simplexml_load_string($gpxData);
        } catch (\Exception $exception) {
            throw new \UnexpectedValueException('Invalid GPX data received!');
        }

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

    private function storeWayPoints(array $wayPoints)
    {
        $repository    = $this->getDoctrine()
            ->getRepository(Waypoint::class);
        $entityManager = $this->getDoctrine()->getManager();

        $currentWayPoints = $repository->findLatLon();

        $cnt = 0;

        foreach ($wayPoints as $wayPoint) {
            $test = $wayPoint->getLat().','.$wayPoint->getLon();
            $x = in_array($test, $currentWayPoints);
            if (true === in_array($wayPoint->getLat().','.$wayPoint->getLon(), $currentWayPoints)) {
                continue;
            }

            $entityManager->persist($wayPoint);
            $entityManager->flush();

            $cnt++;
        }

        return $cnt;
    }

    private function createWayPoint(float $lat, float $lon, string $name, Category $category, ?Province $province = null, ?string $city = '')
    {
        $wayPoint = new Waypoint();

        $wayPoint->setName($name);
        $wayPoint->setLat($lat);
        $wayPoint->setLon($lon);
        $wayPoint->setCategory($category);
        $wayPoint->setProvince($province);
        $wayPoint->setCity($city);

        return $wayPoint;
    }
}