<?php

namespace App\Parser\Type;

use App\Entity\Category;
use App\Entity\Waypoint;
use App\Parser\AbstractParser;

class Json extends AbstractParser
{

    protected function getType(): string
    {
        return 'JsonRaw';
    }

    /**
     * @inheritDoc
     */
    public function parse(array $data): array
    {
        $waypoints = [];

        foreach ($data as $item) {
            $latlng = explode(',', $item->latlng);

            if (2 !== count($latlng)) {
                throw new \UnexpectedValueException('Invalid latlng JSON data');
            }

            $waypoints[] = $this->createWayPoint(
                '',
                (float)$latlng[0],
                (float)$latlng[1],
                $item->title,
            );
        }

        return $waypoints;
    }
}
