<?php
/**
 * User: alec
 * Date: 03.11.18
 * Time: 10:26
 */
declare(strict_types=1);

namespace AlecRabbit;

class EventCounter
{
    protected const DEFAULT_NAME = 'default';
    protected const DEFAULT_LENGTH = 3600;
    protected const DEFAULT_GROUP_BY = 60;

    protected $events = [];

    /** @var int */
    protected $length;

    /** @var string */
    protected $name;

    /** @var int|null */
    protected $groupBy;

    /** @var int */
    protected $lastTimestamp;

    /** @var bool */
    private $relativeMode;

    /**
     * EventCounter constructor.
     * @param int|null $length Time length in seconds e.g. 3600 => 1 hour
     * @param int|null $groupBy Group by period of time in seconds e.g. 60 => 1 min
     * @param bool|null $relativeMode
     */
    public function __construct(?int $length = null, ?int $groupBy = null, ?bool $relativeMode = null)
    {
        $this->name = static::DEFAULT_NAME;
        $this->length = $length ?? static::DEFAULT_LENGTH;
        $this->groupBy = $groupBy;
        $this->relativeMode = $relativeMode ?? false;
    }

    public function addEvent(?int $time = null): void
    {
        $this->lastTimestamp = $time = $time ?? time();
        if (null !== $this->groupBy) {
            $time = base_timestamp($time, $this->groupBy);
        }
        // Is there any event during [$time] period?
        // If not initialize with 0
        $this->events[$time] = $this->events[$time] ?? 0;
        $this->events[$time]++;
        $this->trim();
    }

    private function trim(): void
    {
        $time = $this->relativeMode ? $this->lastTimestamp : time();
        $threshold = $time - $this->length;
        if (($key = array_key_first($this->events)) <= $threshold) {
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return EventCounter
     */
    public function setName(string $name): EventCounter
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set to this mode to count events by timestamps.
     *
     * @param bool $relative
     * @return EventCounter
     */
    public function setRelativeMode(bool $relative = true): EventCounter
    {
        $this->relativeMode = $relative;
        return $this;
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return $this->events;
    }
}
