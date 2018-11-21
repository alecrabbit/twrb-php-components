<?php
/**
 * User: alec
 * Date: 19.11.18
 * Time: 17:08
 */

namespace AlecRabbit;

use AlecRabbit\Event\EventCounterDeprecated;
use AlecRabbit\Structures\Trade;

class TradesCounter
{
    protected const VOLUME = 'volume';

    /** @var string */
    private $pair;

    /**@var EventCounterDeprecated[] */
    private $counters = [];

    /**
     * TradesCounter constructor.
     * @param string $pair
     */
    public function __construct(string $pair)
    {
        foreach (PERIODS as $length => $groupBy) {
            $this->counters[$length] = new EventCounterDeprecated($length, $groupBy);
        }
        $this->pair = $pair;
    }

    public function countTrade(Trade $trade): Trade
    {
        if ($this->pair !== $trade->pair) {
            throw new \RuntimeException('DataInconsistency'); // todo update message
        }
        foreach (PERIODS as $length => $groupBy) {
            $this->counters[$length]->addEvent($trade->timestamp);
        }
//        $this->counters[static::VOLUME]->addTrade($trade);

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
