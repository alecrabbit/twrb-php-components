<?php
/**
 * User: alec
 * Date: 16.11.18
 * Time: 13:41
 */

namespace AlecRabbit\Event;

class EventIndicator
{
    public const THRESHOLD = 60;

    /** @var int */
    private $lastEventTimestamp;

    /**
     * EventIndicator constructor.
     */
    public function __construct()
    {
        $this->event();
    }

    public function event(): void
    {
        $this->lastEventTimestamp = $this->current();
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
            $this->current() - $this->lastEventTimestamp >= 60;
    }
}