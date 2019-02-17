<?php
/**
 * Date: 16.11.18
 * Time: 13:41
 */

namespace AlecRabbit\Event;

class EventIndicator
{
    public const THRESHOLD = SECONDS_IN_01MIN;

    /** @var int */
    private $lastEventTimestamp;
    /** @var int */
    private $threshold;

    /**
     * EventIndicator constructor.
     * @param int|null $threshold
     */
    public function __construct(? int $threshold = null)
    {
        $this->countEvent();
        $this->threshold = $threshold ?? static::THRESHOLD;
    }

    public function countEvent($event = null)
    {
        $this->lastEventTimestamp = $this->current();
        return $event;
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
    public function isNotOk(): bool
    {
        return
            !$this->isOk();
    }

    /**
     * @return bool
     */
    public function isOk(): bool
    {
        return
            $this->current() - $this->lastEventTimestamp <= $this->threshold;
    }
}
