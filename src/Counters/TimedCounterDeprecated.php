<?php
/**
 * User: alec
 * Date: 19.11.18
 * Time: 17:30
 */
declare(strict_types=1);

namespace AlecRabbit\Counters;

use AlecRabbit\Traits\Nameable;

/**
 * Class TimedCounterDeprecated
 * @package AlecRabbit\Counters
 * @deprecated
 */
class TimedCounterDeprecated
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
     * @param bool $relativeMode
     */
    public function __construct(?int $length = P_01HOUR, ?int $groupBy = null, bool $relativeMode = false)
    {
        $this->setName(static::DEFAULT_NAME);

        $this->length = $length ?? static::DEFAULT_LENGTH;
        $this->groupBy = $groupBy;
        $this->relativeMode = $relativeMode;
        $this->lastTimestamp = 0;
    }

    /**
     * Enables relative time mode
     * @return self
     */
    public function enableRelativeMode(): self
    {
        return $this->setRelativeMode(true);
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

    /**
     * Disables relative time mode
     * @return self
     */
    public function disableRelativeMode(): self
    {
        return $this->setRelativeMode(false);
    }

    /**
     * @param int|null $timestamp
     * @return int
     */
    protected function getBaseTime(?int $timestamp = null): int
    {
        $this->lastTimestamp = $time = $timestamp ?? time();
        if (null !== $this->groupBy && $this->groupBy > ONE_SECOND) {
            $time = base_timestamp($time, $this->groupBy);
        }
        return $time;
    }

    /**
     * @return int
     */
    protected function getTime(): int
    {
        return
            $this->relativeMode ? $this->lastTimestamp : time();
    }

    /**
     * @return int
     */
    protected function getThreshold(): int
    {
        return
            $this->getTime() - $this->length;
    }
}
