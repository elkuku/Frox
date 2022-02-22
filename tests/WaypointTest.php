<?php

namespace App\Tests;

use App\Service\WayPointHelper;
use PHPUnit\Framework\TestCase;

class WaypointTest extends TestCase
{
    public function testAdd(): void
    {
        $helper = new WayPointHelper('tests/testdir', 'fooo');

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
