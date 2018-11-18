<?php
/**
 * User: alec
 * Date: 16.11.18
 * Time: 13:41
 */

namespace AlecRabbit\Event;

class EventIndicator
{
    public const THRESHOLD = SECONDS_IN_01MIN;

    /** @var int */
    private $lastEventTimestamp;

    /**
     * EventIndicator constructor.
     */
    public function __construct()
    {
        $this->countEvent();
    }

    public function countEvent($event = null)
    {
        $this->lastEventTimestamp = $this->current();
        if (null !== $event) {
            return $event;
        }
    }

    /**
     * @return int
     */
    private function current(): int
    {
        return time();
    }

    /**
     * @return bool
     */
    public function isOk(): bool
    {
        return
            $this->current() - $this->lastEventTimestamp >= static::THRESHOLD;
    }
}
