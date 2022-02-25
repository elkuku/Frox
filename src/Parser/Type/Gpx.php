<?php

namespace App\Parser\Type;

use App\Entity\Category;
use App\Entity\Waypoint;
use App\Parser\AbstractParser;

class Gpx extends AbstractParser
{

    protected function getType(): string
    {
        return 'gpxRaw';
    }

    /**
     * @inheritDoc
     */
    public function parse(array $data): array
    {
        try {
            $xml = simplexml_load_string($dagpxData);
        } catch (Exception $exception) {
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
}
