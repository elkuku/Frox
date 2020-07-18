<?php

namespace App\Parser\Type;

use App\Entity\Category;
use App\Entity\Waypoint;
use App\Parser\AbstractParser;

class Csv extends AbstractParser
{
    protected function getType(): string
    {
        return 'csvRaw';
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

            if (0 === $i || !$line) {
                continue;
            }

            $parts = explode(',', $line);

            if (4 !== \count($parts)) {
                $parts = $this->parseFishyCsvLine($parts);
                if (4 !== \count($parts)) {
                    throw new \UnexpectedValueException('Error parsing CSV file');
                }
            }

            $lat = (float)$parts[1];
            $lon = (float)$parts[2];

            $wayPoint = $repository->findOneBy(
                [
                    'lat' => $lat,
                    'lon' => $lon,
                ]
            );

            if (!$wayPoint) {
                $wayPoint = new Waypoint();

                $wayPoint->setName($parts[0]);
                $wayPoint->setLat($lat);
                $wayPoint->setLon($lon);
                $wayPoint->setCategory($category);
                $wayPoint->setProvince($province);
                $wayPoint->setCity($city);

                $entityManager->persist($wayPoint);

                $entityManager->flush();

                $cnt++;
            }

            // Check image
            $wayPointHelper->checkImage($wayPoint->getId(), trim($parts[3]));
        }

        return $cnt;
    }

    private function parseFishyCsvLine(array $parts): array
    {
        $returnValues = [];

        $cnt = \count($parts);

        $returnValues[3] = $parts[$cnt - 1];
        unset ($parts[$cnt - 1]);

        $returnValues[2] = $parts[$cnt - 2];
        unset ($parts[$cnt - 2]);

        $returnValues[1] = $parts[$cnt - 3];
        unset ($parts[$cnt - 3]);

        $returnValues[0] = implode('', $parts);

        return $returnValues;
    }
}
