<?php

namespace App\Parser\Type;

use App\Entity\Category;
use App\Entity\Waypoint;
use App\Parser\AbstractParser;

class MultiExportCsv extends AbstractParser
{
    protected function getType(): string
    {
        return 'multiexportcsv';
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

            $parts = explode(',', $line);

            if (3 !== \count($parts)) {
                $parts = $this->parseFishyCsvLine2($line);
                if (3 !== \count($parts)) {
                    throw new \UnexpectedValueException(
                        'Error parsing CSV file'
                    );
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

                $wayPoint->setName(trim($parts[0], '""'));
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
            // $wayPointHelper->checkImage($wayPoint->getId(), trim($parts[3]));
        }

        return $cnt;
    }

    private function parseFishyCsvLine2(string $line)
    {
        $parts = explode(',', $line);

        $newParts = [];

        $newParts[2] = array_pop($parts);
        $newParts[1] = array_pop($parts);
        $newParts[0] = implode(',', $parts);

        return $newParts;
    }
}
