<?php
/**
 * User: alec
 * Date: 19.11.18
 * Time: 17:17
 */

namespace AlecRabbit\Counters;

class EventsCounter extends TimedCounter
{
    /**
     * Alias to add() method
     */
    public function addEvent(?int $time = null): void
    {
        $this->add($time);
    }

    public function getRawEventsData(): array
    {
        return $this->getRawData();
    }

    public function getEventsArray(?bool $reset = null): array
    {
        return $this->getDataArray($reset);
    }

    public function getEvents(?bool $reset = null): object
    {
        return $this->getDataObject($reset);
    }
}
