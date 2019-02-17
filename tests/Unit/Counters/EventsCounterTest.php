<?php
/**
 * Date: 03.11.18
 * Time: 22:32
 */

namespace Unit\Counters;

use AlecRabbit\Counters\EventsCounter;
use PHPUnit\Framework\TestCase;

class EventsCounterTest extends TestCase
{
    /** @test */
    public function creationEventCounter(): void
    {
        $eventsCounter = new EventsCounter();
        $eventsCounter->setName('new');
        $this->assertEquals('new', $eventsCounter->getName());
        $this->assertEquals([], $eventsCounter->getRawEventsData());
        $eventsCounter->addEvent();
        $expected =
            [
                P_01MIN => 1,
                P_03MIN => 1,
                P_05MIN => 1,
                P_15MIN => 1,
                P_30MIN => 1,
                P_45MIN => 1,
                P_01HOUR => 1,
                P_02HOUR => 1,
                P_03HOUR => 1,
                P_04HOUR => 1,
                P_01DAY => 1,
            ];
        $this->assertEquals($expected, $eventsCounter->getEventsArray());
        $eventsCounter->addEvent();
        $expected =
            [
                P_01MIN => 2,
                P_03MIN => 2,
                P_05MIN => 2,
                P_15MIN => 2,
                P_30MIN => 2,
                P_45MIN => 2,
                P_01HOUR => 2,
                P_02HOUR => 2,
                P_03HOUR => 2,
                P_04HOUR => 2,
                P_01DAY => 2,
            ];
        $this->assertEquals($expected, $eventsCounter->getEventsArray());
        $expectedObject = new \stdClass();
        $expectedObject->{P_01MIN} = 2;
        $expectedObject->{P_03MIN} = 2;
        $expectedObject->{P_05MIN} = 2;
        $expectedObject->{P_15MIN} = 2;
        $expectedObject->{P_30MIN} = 2;
        $expectedObject->{P_45MIN} = 2;
        $expectedObject->{P_01HOUR} = 2;
        $expectedObject->{P_02HOUR} = 2;
        $expectedObject->{P_03HOUR} = 2;
        $expectedObject->{P_04HOUR} = 2;
        $expectedObject->{P_01DAY} = 2;
        $this->assertEquals($expectedObject, $eventsCounter->getEvents());
    }
}
