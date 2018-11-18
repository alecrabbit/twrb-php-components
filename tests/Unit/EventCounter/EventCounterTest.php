<?php
/**
 * User: alec
 * Date: 03.11.18
 * Time: 22:32
 */

namespace Unit\EventCounter;

use AlecRabbit\Event\EventCounter;
use PHPUnit\Framework\TestCase;
use Unit\DataProviders\EventsBasicDataProviderOne;
use Unit\DataProviders\EventsBasicDataProviderTwo;

class EventCounterTest extends TestCase
{
    /** @test */
    public function creationEventCounter(): void
    {
        $ec = new EventCounter(3600, 60);
        $ec->setName('new');
        $this->assertEquals('new', $ec->getName());
        $this->assertEquals([], $ec->getEvents());
        $ec->addEvent();
        $this->assertEquals(1, $ec->getCalculatedEvents());
        $ec->addEvent();
        $this->assertEquals(2, $ec->getCalculatedEvents());
    }

    /** @test */
    public function fillEventCounter(): void
    {
        $ec = new EventCounter(86400, 60);
        $ec->setRelativeMode();
        foreach (EventsBasicDataProviderOne::data() as $timestamp) {
            $ec->addEvent($timestamp);
        }
        $this->assertEquals(5760, $ec->getCalculatedEvents());
        $this->assertCount(1440, $ec->getEvents());
        $this->assertEquals(5760, $ec->getCalculatedEvents(true));
        $this->assertCount(0, $ec->getEvents());
    }

    /** @test */
    public function fillEventCounter2(): void
    {
        $ec = new EventCounter( 60);
        $ec->setRelativeMode();
        foreach (EventsBasicDataProviderTwo::data() as $timestamp) {
            $ec->addEvent($timestamp);
        }
        $this->assertEquals(120, $ec->getCalculatedEvents());
        $this->assertCount(60, $ec->getEvents());
        $this->assertEquals(120, $ec->getCalculatedEvents(true));
        $this->assertCount(0, $ec->getEvents());
    }
}
