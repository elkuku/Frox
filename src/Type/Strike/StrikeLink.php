<?php

namespace App\Type\Strike;

class StrikeLink
{
    public $originName = '';
    public $originLat = 0;
    public $originLon = 0;

    public $destinationName = '';
    public $destinationLat = 0;
    public $destinationLon = 0;

    public function __construct(
        string $originName = '',
        float $originLat = 0.0,
        float $originLon = 0.0,
        string $destinationName = '',
        float $destinationLat = 0.0,
        float $destinationLon = 0.0
    ) {
        $this->originName = $originName;
        $this->originLat = $originLat;
        $this->originLon = $originLon;

        $this->destinationName = $destinationName;
        $this->destinationLat = $destinationLat;
        $this->destinationLon = $destinationLon;
    }
}
