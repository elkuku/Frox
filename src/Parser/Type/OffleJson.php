<?php

namespace App\Parser\Type;

use App\Entity\Category;
use App\Entity\Waypoint;
use App\Parser\AbstractParser;

class OffleJson extends AbstractParser
{

    protected function getType(): string
    {
        return 'OffleJson';
    }

    /**
     * @inheritDoc
     */
    public function parse(array $data): array
    {
        $waypoints = [];
        $jsonData = json_decode($data, false);

        if (!$jsonData) {
            throw new \UnexpectedValueException('Invalid JSON data received');
        }

        foreach ($jsonData as $item) {
            if (isset($item->name)) {
                $waypoints[] = $this->createWayPoint(
                    $item->guid,
                    $item->lat,
                    $item->lng,
                    $item->name,
                );
            }
        }

        return $waypoints;
    }
}
