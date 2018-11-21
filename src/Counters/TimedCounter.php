<?php
/**
 * User: alec
 * Date: 19.11.18
 * Time: 17:30
 */
declare(strict_types=1);

namespace AlecRabbit\Counters;

use AlecRabbit\Traits\Nameable;

class TimedCounter
{
    protected const DEFAULT_NAME = 'default';
    protected const DEFAULT_LENGTH = 3600;
    protected const DEFAULT_GROUP_BY = 60;

    use Nameable;

    /** @var int */
    protected $length;

    /** @var int|null */
    protected $groupBy;

    /** @var int */
    protected $lastTimestamp;

    /** @var bool */
    protected $relativeMode;

    /**
     * BasicCounter constructor.
     * @param int|null $length
     * @param int|null $groupBy
     * @param bool|null $relativeMode
     */
    public function __construct(?int $length = null, ?int $groupBy = null, ?bool $relativeMode = null)
    {
        $this->name = static::DEFAULT_NAME;

        $this->length = $length ?? static::DEFAULT_LENGTH;
        $this->groupBy = $groupBy;
        $this->relativeMode = $relativeMode ?? false;
        $this->lastTimestamp = 0;
    }

    /**
     * Set to this mode to count by timestamps.
     *
     * @param bool $relative
     * @return self
     */
    public function setRelativeMode(bool $relative = true): self
    {
        $this->relativeMode = $relative;
        return $this;
    }

    protected function getTime(?int $timestamp = null): int
    {
        $this->lastTimestamp = $time = $timestamp ?? time();
        if (null !== $this->groupBy) {
            $time = base_timestamp($time, $this->groupBy);
        }
        return $time;
    }
}
