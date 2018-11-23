<?php
/**
 * User: alec
 * Date: 03.11.18
 * Time: 10:26
 */
declare(strict_types=1);

namespace AlecRabbit\Event;

use AlecRabbit\Counters\TimedCounterDeprecated;

class EventCounterDeprecated extends TimedCounterDeprecated
{
    /** @var array */
    protected $events = [];

    /**
     * @param int|null $time
     */
    public function addEvent(?int $time = null): void
    {
        $time = $this->getBaseTime($time);
        // Is there any event during [$time] period? If not initialize with 0
        $this->events[$time] = $this->events[$time] ?? 0;
        $this->events[$time]++;
        $this->trim();
    }

    private function trim(): void
    {
        if (null !== ($key = array_key_first($this->events))
            && ($key <= $this->getThreshold())) {
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
            $this->reset();
        }
        return $r;
    }

    protected function reset(): void
    {
        $this->events = [];
    }

    /**
     * @return array
     */
    public function getRawEventsData(): array
    {
        return $this->events;
    }
}
