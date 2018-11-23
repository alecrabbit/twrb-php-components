<?php
/**
 * User: alec
 * Date: 03.11.18
 * Time: 22:32
 */

namespace Unit\EventCounter\Event;

use AlecRabbit\Event\EventCounterDeprecated;
use PHPUnit\Framework\TestCase;
use Unit\DataProviders\EventsBasicDataProviderOne;
use Unit\DataProviders\EventsBasicDataProviderTwo;

class EventCounterTest extends TestCase
{
    /** @test */
    public function creationEventCounter(): void
    {
        $ec = new EventCounterDeprecated(3600, 60);
        $ec->setName('new');
        $this->assertEquals('new', $ec->getName());
        $this->assertEquals([], $ec->getRawEventsData());
        $ec->addEvent();
        $this->assertEquals(1, $ec->getCalculatedEvents());
        $ec->addEvent();
        $this->assertEquals(2, $ec->getCalculatedEvents());
    }

    /** @test */
    public function fillEventCounter(): void
    {
        $ec = new EventCounterDeprecated(86400, 60);
        $ec->setRelativeMode();
        foreach (EventsBasicDataProviderOne::data() as $timestamp) {
            $ec->addEvent($timestamp);
        }
        $this->assertEquals(5760, $ec->getCalculatedEvents());
        $this->assertCount(1440, $ec->getRawEventsData());
        $this->assertEquals(5760, $ec->getCalculatedEvents(true));
        $this->assertCount(0, $ec->getRawEventsData());
    }

    /** @test */
    public function fillEventCounter2(): void
    {
        $ec = new EventCounterDeprecated(60);
        $ec->setRelativeMode();
        foreach (EventsBasicDataProviderTwo::data() as $timestamp) {
            $ec->addEvent($timestamp);
        }
        $this->assertEquals(120, $ec->getCalculatedEvents());
        $this->assertCount(60, $ec->getRawEventsData());
        $this->assertEquals(120, $ec->getCalculatedEvents(true));
        $this->assertCount(0, $ec->getRawEventsData());
    }
}
