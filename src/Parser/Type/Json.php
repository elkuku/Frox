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
        // $jsonData = json_decode($JsonRaw, false);
        //
        // if (!$jsonData) {
        //     throw new \UnexpectedValueException('Invalid JSON data received');
        // }

        foreach ($data as $item) {
            $latlng = explode(',', $item->latlng);

            if (2 != count($latlng)) {
                throw new \UnexpectedValueException('Invalid latlng JSON data');
            }

            $waypoints[] = $this->createWayPoint(
                '',
                $latlng[0],
                $latlng[1],
                $item->title,
            );
        }

        return $waypoints;
    }
}
