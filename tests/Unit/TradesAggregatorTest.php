<?php
/**
 * User: alec
 * Date: 19.11.18
 * Time: 14:41
 */

namespace Tests;

use AlecRabbit\Structures\Trade;
use AlecRabbit\TradesAggregator;
use PHPUnit\Framework\TestCase;

class TradesAggregatorTest extends TestCase
{
    /** @test */
    public function instance(): void
    {
        $object = new TradesAggregator('btc_usd');
        $this->assertInstanceOf(TradesAggregator::class, $object);
    }

    /** @test */
    public function countTrade(): void
    {
        $pair = 'btc_usd';
        $object = new TradesAggregator($pair);
        $trade = new Trade(T_SELL, $pair, 0.1, 8500.0);
        $object->countTrade($trade);
        $this->assertEquals(1, $object->trades(P_01DAY));
        $this->assertEquals(0.1, $object->volume(P_01DAY));
        $this->assertEquals(8500, $object->avgPrice(P_01DAY));
    }
}