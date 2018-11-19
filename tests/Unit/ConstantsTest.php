<?php
/**
 * User: alec
 * Date: 09.11.18
 * Time: 13:23
 */

namespace Tests\Unit;


use PHPUnit\Framework\TestCase;

class ConstantsTest extends TestCase
{

    /** @test */
    public function setProperly(): void
    {
        $this->assertEquals(60, RESOLUTION_01MIN);
        $this->assertEquals(180, RESOLUTION_03MIN);
        $this->assertEquals(300, RESOLUTION_05MIN);
        $this->assertEquals(900, RESOLUTION_15MIN);
        $this->assertEquals(1800, RESOLUTION_30MIN);
        $this->assertEquals(2700, RESOLUTION_45MIN);
        $this->assertEquals(3600, RESOLUTION_01HOUR);
        $this->assertEquals(7200, RESOLUTION_02HOUR);
        $this->assertEquals(10800, RESOLUTION_03HOUR);
        $this->assertEquals(14400, RESOLUTION_04HOUR);
        $this->assertEquals(86400, RESOLUTION_01DAY);
        $this->assertEquals([60, 180, 300, 900, 1800, 2700, 3600, 7200, 10800, 14400, 86400], RESOLUTIONS);
        $this->assertEquals(
            [
                60 => '01m',
                180 => '03m',
                300 => '05m',
                900 => '15m',
                1800 => '30m',
                2700 => '45m',
                3600 => '01h',
                7200 => '02h',
                10800 => '03h',
                14400 => '04h',
                86400 => '01d',
            ],
            RESOLUTION_ALIASES
        );

    }
}