<?php
/**
 * User: alec
 * Date: 03.11.18
 * Time: 10:26
 */
declare(strict_types=1);

namespace AlecRabbit\Event;

use AlecRabbit\Counters\TimedCounter;

class EventCounter extends TimedCounter
{
    /** @var array */
    protected $events = [];

    /**
     * @param int|null $time
     */
    public function addEvent(?int $time = null): void
    {
        $time = $this->getTime($time);
        // Is there any event during [$time] period? If not initialize with 0
        $this->events[$time] = $this->events[$time] ?? 0;
        $this->events[$time]++;
        $this->trim();
    }

    private function trim(): void
    {
        $time = $this->relativeMode ? $this->lastTimestamp : time();
        $threshold = $time - $this->length;
        if (null !== ($key = array_key_first($this->events)) && ($key <= $threshold)) {
            unset($this->events[$key]);
        }
    }

    /**
     * @param bool|null $reset
     * @return int
     */
    public function getCalculatedEvents(?bool $reset = null): int
    {
        $r = 0;
        if (0 < ($sum = (int)array_sum($this->events))) {
            $r = $sum;
        }
        if ($reset) {
            $this->events = [];
        }
        return $r;
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return $this->events;
    }
}
