<?php

namespace App\Tests;

use App\Service\WayPointHelper;
use PHPUnit\Framework\TestCase;

class WaypointTest extends TestCase
{
    public function testAdd()
    {
        $helper = new WayPointHelper('tests/testdir');

        $this->assertEquals(
            'test',
            $helper->cleanName(' test ')
        );

        // á, é, í, ó, ú, ñ, ü
        $this->assertEquals(
            'test',
            $helper->cleanName('tést')
        );

        $this->assertEquals(
            'hola nianio',
            $helper->cleanName('hola ñaño')
        );

        $this->assertEquals(
            '',
            $helper->cleanName(',.;:"\'\\')
        );

    }
}
