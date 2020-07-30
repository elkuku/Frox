<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 16.10.18
 * Time: 10:22
 */

namespace App\Type;

class InfoKeyPrepType
{
    /**
     * @var WayPointPrepType[]
     */
    private array $wayPoints = [];

    public function addWayPoint(WayPointPrepType $wayPoint): self
    {
        $this->wayPoints[] = $wayPoint;

        usort(
            $this->wayPoints,
            static function ($a, $b) {
                return $a->mapNo - $b->mapNo;
            }
        );

        return $this;
    }

    /**
     * @return WayPointPrepType[]
     */
    public function getWayPoints(): array
    {
        return $this->wayPoints;
    }
}
