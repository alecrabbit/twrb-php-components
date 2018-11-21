<?php
/**
 * User: alec
 * Date: 05.11.18
 * Time: 17:05
 */
declare(strict_types=1);

namespace AlecRabbit;

use AlecRabbit\Event\EventCounter;
use AlecRabbit\Structures\Trade;

class TradesAggregator
{
    private const PERIODS = [
        P_01MIN => P_01MIN,
        P_03MIN => P_01MIN,
        P_05MIN => P_01MIN,
        P_15MIN => P_05MIN,
        P_30MIN => P_05MIN,
        P_45MIN => P_05MIN,
        P_01HOUR => P_15MIN,
        P_02HOUR => P_15MIN,
        P_03HOUR => P_15MIN,
        P_04HOUR => P_15MIN,
        P_01DAY => P_01HOUR,
    ];

    /** @var string */
    private $pair;

    /**@var EventCounter[] */
    private $counters = [];

    /**
     * TradesCounter constructor.
     * @param string $pair
     */
    public function __construct(string $pair)
    {
        foreach (static::PERIODS as $length => $groupBy) {
            $this->counters[$length] = new EventCounter($length, $groupBy);
        }
        $this->pair = $pair;
    }

    public function countTrade(Trade $trade): Trade
    {
        if ($this->pair !== $trade->pair) {
            throw new \RuntimeException('DataInconsistency'); // todo update message
        }
        foreach (static::PERIODS as $length => $groupBy) {
            $this->counters[$length]->addEvent($trade->timestamp);
        }
        return $trade;
    }

    public function trades(int $timePeriod): int
    {
        return $this->counters[$timePeriod]->getCalculatedEvents();
    }

    public function volume(int $timePeriod): float
    {
        return 0.1;
    }

    public function avgPrice(int $timePeriod): float
    {
        return 8500;
    }
}
