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

        $this->assertEquals(
            '',
            $helper->cleanName(',.;:"\'')
        );

    }
}
