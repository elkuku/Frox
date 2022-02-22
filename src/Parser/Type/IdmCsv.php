<?php

namespace App\Parser\Type;

use App\Entity\Category;
use App\Entity\Waypoint;
use App\Parser\AbstractParser;

class IdmCsv extends AbstractParser
{

    protected function getType(): string
    {
        return 'idmcsvRaw';
    }

    /**
     * @inheritDoc
     */
    public function parse(array $data): array
    {
        $repository = $this->getDoctrine()
            ->getRepository(Waypoint::class);

        $entityManager = $this->getDoctrine()->getManager();

        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['id' => 1]);

        $lines = explode("\n", $csvRaw);
        $cnt = 0;

        foreach ($lines as $i => $line) {
            $line = trim($line);

            if (!$line) {
                continue;
            }

            $parts = explode(',', $line);

            if (3 !== \count($parts)) {
                throw new \UnexpectedValueException(
                    'Error parsing Idm CSV file'
                );
            }

            $lat = (float)$parts[1];
            $lon = (float)$parts[2];

            $wayPoint = $repository->findOneBy(['lat' => $lat, 'lon' => $lon,]);

            if (!$wayPoint) {
                $wayPoint = new Waypoint();

                $wayPoint->setName(trim($parts[0], '"'));
                $wayPoint->setLat($lat);
                $wayPoint->setLon($lon);
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
