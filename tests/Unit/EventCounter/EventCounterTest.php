<?php
/**
 * User: alec
 * Date: 03.11.18
 * Time: 22:32
 */

namespace Unit\EventCounter;

use AlecRabbit\EventCounter;
use PHPUnit\Framework\TestCase;

class EventCounterTest extends TestCase
{
    /** @test */
    public function creationEventCounter(): void
    {
        $ec = new EventCounter(3600, 60, 'new');
        $this->assertEquals('new', $ec->getName());
        $this->assertEquals([], $ec->getEvents());
        $ec->addEvent();
        $this->assertEquals(1, $ec->getCalculatedEvents());
        $ec->addEvent();
        $this->assertEquals(2, $ec->getCalculatedEvents());
    }
}
