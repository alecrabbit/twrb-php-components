<?php
/**
 * User: alec
 * Date: 19.11.18
 * Time: 16:00
 */

namespace AlecRabbit\Statistics;

use AlecRabbit\Structures\Trade;
use AlecRabbit\TradesAggregator;

class Statistics
{
    private $tradeCounters = [];

    public function countTrade(Trade $trade): Trade
    {
        $pair = $trade->pair;
        $counter = $this->tradeCounters[$pair] ?? $this->tradeCounters[$pair] = new TradesAggregator($pair);
        $counter->countTrade($trade);
        return $trade;
    }
}
