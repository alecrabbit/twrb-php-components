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
    protected const DEFAULT_SIZE = 3600;
    protected const DEFAULT_LENGTH = 60;

    protected $events = [];
    /** @var int */
    protected $size;
    /** @var int */
    protected $length;
    /** @var string */
    protected $name;

    /**
     * EventCounter constructor.
     * @param null|int $size Element count
     * @param null|int $length Period in seconds
     * @param null|string $name
     */
    public function __construct(?int $size = null, ?int $length = null, ?string $name = null)
    {
        $this->name = $name ?? static::DEFAULT_NAME;
        $this->size = $size ?? static::DEFAULT_SIZE;
        $this->length = $length ?? static::DEFAULT_LENGTH;
    }


    public function addEvent(?int $time = null): void
    {
        $time = $time ?? time();
        // Is there any event during [$time] second if not initialize with 0
        $this->events[$time] = $this->events[$time] ?? 0;
        $this->events[$time]++;
        if (\count($this->events) > $this->size) {
            reset($this->events);
            $key = key($this->events);
            unset($this->events[$key]);
        }
    }

    /**
     * @param bool|null $reset
     * @param bool|null $relative
     * @return int
     */
    public function getCalculatedEvents(?bool $reset = null, ?bool $relative = null): int
    {
        $this->trim($relative);
        $r = 0;
        if (0 < ($sum = array_sum($this->events))) {
            $r = $sum;
        }
        if ($reset) {
            $this->events = [];
        }

        return $r;
    }

    public function trim(?bool $relative = null): void
    {
        $time = $relative ? $this->lastTimestamp() : time();
        $threshold = $time - $this->length;
        foreach ($this->events as $t => $count) {
            if ($t < $threshold) {
                unset($this->events[$t]);
            }
        }

    }

    private function lastTimestamp(): int
    {
        end($this->events);
        return key($this->events);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

}