<?php
/**
 * User: alec
 * Date: 19.11.18
 * Time: 20:06
 */

namespace Tests\Unit\Counters;

use AlecRabbit\Circular;
use AlecRabbit\Counters\VolumeCounter;
use AlecRabbit\Rewindable;
use AlecRabbit\Structures\Trade;
use PHPUnit\Framework\TestCase;
use Tests\Unit\DataProviders\CommonTrades;

class VolumeCounterTest extends TestCase
{
    /** @var Rewindable */
    private static $data;
    /** @var string */
    private static $pair;

    /** @var VolumeCounter */
    private $counter;

    public static function setUpBeforeClass()
    {
        static::$pair = 'btc_usd';
        static::$data = new Rewindable(
            [CommonTrades::class, 'generator'],
            new Circular([T_ASK, T_BID]),
            new Circular([static::$pair]),
            new Circular(
                [10000,]
            ),
            new Circular([0.001,])
        );
    }

    /** @test */
    public function instance(): void
    {
        $this->counter = new VolumeCounter();
        $this->assertInstanceOf(VolumeCounter::class, $this->counter);
        $this->assertEquals(DEFAULT_NAME, $this->counter->getName());
        $this->assertEquals([], $this->counter->getVolumeArray());
        $this->assertEquals([], $this->counter->getEventsArray());
        $expected = [
            STR_VOLUMES => [],
            STR_EVENTS => [],
        ];
        $this->assertEquals($expected, $this->counter->getRawData());
    }

    /** @test */
    public function instanceWithSimpleAdd(): void
    {
        $this->counter = new VolumeCounter();
        $this->counter->enableRelativeMode();
        $this->counter->addTrade(
            new Trade(T_SELL, 'btc_usd', 10000, 0.001, 1514764800)
        );
        $this->counter->addTrade(
            new Trade(T_SELL, 'btc_usd', 10000, 0.011, 1514764800)
        );
        $this->counter->addTrade(
            new Trade(T_BUY, 'btc_usd', 10000, 0.04, 1514764800)
        );
        $expected = [
            STR_VOLUMES => [
                STR_TOTAL =>
                    [
                        P_01MIN => [1514764800 => 0.052,],
                        P_03MIN => [1514764800 => 0.052,],
                        P_05MIN => [1514764800 => 0.052,],
                        P_15MIN => [1514764800 => 0.052,],
                        P_30MIN => [1514764800 => 0.052,],
                        P_45MIN => [1514764800 => 0.052,],
                        P_01HOUR => [1514764800 => 0.052,],
                        P_02HOUR => [1514764800 => 0.052,],
                        P_03HOUR => [1514764800 => 0.052,],
                        P_04HOUR => [1514764800 => 0.052,],
                        P_01DAY => [1514764800 => 0.052,],
                    ],
                STR_SELL =>
                    [
                        P_01MIN => [1514764800 => 0.012,],
                        P_03MIN => [1514764800 => 0.012,],
                        P_05MIN => [1514764800 => 0.012,],
                        P_15MIN => [1514764800 => 0.012,],
                        P_30MIN => [1514764800 => 0.012,],
                        P_45MIN => [1514764800 => 0.012,],
                        P_01HOUR => [1514764800 => 0.012,],
                        P_02HOUR => [1514764800 => 0.012,],
                        P_03HOUR => [1514764800 => 0.012,],
                        P_04HOUR => [1514764800 => 0.012,],
                        P_01DAY => [1514764800 => 0.012,],
                    ],
                STR_BUY =>
                    [
                        P_01MIN => [1514764800 => 0.04,],
                        P_03MIN => [1514764800 => 0.04,],
                        P_05MIN => [1514764800 => 0.04,],
                        P_15MIN => [1514764800 => 0.04,],
                        P_30MIN => [1514764800 => 0.04,],
                        P_45MIN => [1514764800 => 0.04,],
                        P_01HOUR => [1514764800 => 0.04,],
                        P_02HOUR => [1514764800 => 0.04,],
                        P_03HOUR => [1514764800 => 0.04,],
                        P_04HOUR => [1514764800 => 0.04,],
                        P_01DAY => [1514764800 => 0.04,],
                    ],
                STR_TOTAL_VP =>
                    [
                        P_01MIN => [1514764800 => 520.0,],
                        P_03MIN => [1514764800 => 520.0,],
                        P_05MIN => [1514764800 => 520.0,],
                        P_15MIN => [1514764800 => 520.0,],
                        P_30MIN => [1514764800 => 520.0,],
                        P_45MIN => [1514764800 => 520.0,],
                        P_01HOUR => [1514764800 => 520.0,],
                        P_02HOUR => [1514764800 => 520.0,],
                        P_03HOUR => [1514764800 => 520.0,],
                        P_04HOUR => [1514764800 => 520.0,],
                        P_01DAY => [1514764800 => 520.0,],
                    ],
                STR_SELL_VP =>
                    [
                        P_01MIN => [1514764800 => 120.0,],
                        P_03MIN => [1514764800 => 120.0,],
                        P_05MIN => [1514764800 => 120.0,],
                        P_15MIN => [1514764800 => 120.0,],
                        P_30MIN => [1514764800 => 120.0,],
                        P_45MIN => [1514764800 => 120.0,],
                        P_01HOUR => [1514764800 => 120.0,],
                        P_02HOUR => [1514764800 => 120.0,],
                        P_03HOUR => [1514764800 => 120.0,],
                        P_04HOUR => [1514764800 => 120.0,],
                        P_01DAY => [1514764800 => 120.0,],
                    ],
                STR_BUY_VP =>
                    [
                        P_01MIN => [1514764800 => 400.0,],
                        P_03MIN => [1514764800 => 400.0,],
                        P_05MIN => [1514764800 => 400.0,],
                        P_15MIN => [1514764800 => 400.0,],
                        P_30MIN => [1514764800 => 400.0,],
                        P_45MIN => [1514764800 => 400.0,],
                        P_01HOUR => [1514764800 => 400.0,],
                        P_02HOUR => [1514764800 => 400.0,],
                        P_03HOUR => [1514764800 => 400.0,],
                        P_04HOUR => [1514764800 => 400.0,],
                        P_01DAY => [1514764800 => 400.0,],
                    ],
                STR_P_SUM_TOTAL =>
                    [
                        P_01MIN => [1514764800 => 30000,],
                        P_03MIN => [1514764800 => 30000,],
                        P_05MIN => [1514764800 => 30000,],
                        P_15MIN => [1514764800 => 30000,],
                        P_30MIN => [1514764800 => 30000,],
                        P_45MIN => [1514764800 => 30000,],
                        P_01HOUR => [1514764800 => 30000,],
                        P_02HOUR => [1514764800 => 30000,],
                        P_03HOUR => [1514764800 => 30000,],
                        P_04HOUR => [1514764800 => 30000,],
                        P_01DAY => [1514764800 => 30000,],
                    ],
                STR_P_SUM_SELL =>
                    [
                        P_01MIN => [1514764800 => 20000,],
                        P_03MIN => [1514764800 => 20000,],
                        P_05MIN => [1514764800 => 20000,],
                        P_15MIN => [1514764800 => 20000,],
                        P_30MIN => [1514764800 => 20000,],
                        P_45MIN => [1514764800 => 20000,],
                        P_01HOUR => [1514764800 => 20000,],
                        P_02HOUR => [1514764800 => 20000,],
                        P_03HOUR => [1514764800 => 20000,],
                        P_04HOUR => [1514764800 => 20000,],
                        P_01DAY => [1514764800 => 20000,],
                    ],
                STR_P_SUM_BUY =>
                    [
                        P_01MIN => [1514764800 => 10000,],
                        P_03MIN => [1514764800 => 10000,],
                        P_05MIN => [1514764800 => 10000,],
                        P_15MIN => [1514764800 => 10000,],
                        P_30MIN => [1514764800 => 10000,],
                        P_45MIN => [1514764800 => 10000,],
                        P_01HOUR => [1514764800 => 10000,],
                        P_02HOUR => [1514764800 => 10000,],
                        P_03HOUR => [1514764800 => 10000,],
                        P_04HOUR => [1514764800 => 10000,],
                        P_01DAY => [1514764800 => 10000,],
                    ],
            ],
            STR_EVENTS => [
                STR_TOTAL =>
                    [
                        P_01MIN => [1514764800 => 3,],
                        P_03MIN => [1514764800 => 3,],
                        P_05MIN => [1514764800 => 3,],
                        P_15MIN => [1514764800 => 3,],
                        P_30MIN => [1514764800 => 3,],
                        P_45MIN => [1514764800 => 3,],
                        P_01HOUR => [1514764800 => 3,],
                        P_02HOUR => [1514764800 => 3,],
                        P_03HOUR => [1514764800 => 3,],
                        P_04HOUR => [1514764800 => 3,],
                        P_01DAY => [1514764800 => 3,],
                    ],
                STR_SELL =>
                    [
                        P_01MIN => [1514764800 => 2,],
                        P_03MIN => [1514764800 => 2,],
                        P_05MIN => [1514764800 => 2,],
                        P_15MIN => [1514764800 => 2,],
                        P_30MIN => [1514764800 => 2,],
                        P_45MIN => [1514764800 => 2,],
                        P_01HOUR => [1514764800 => 2,],
                        P_02HOUR => [1514764800 => 2,],
                        P_03HOUR => [1514764800 => 2,],
                        P_04HOUR => [1514764800 => 2,],
                        P_01DAY => [1514764800 => 2,],
                    ],
                STR_BUY =>
                    [
                        P_01MIN => [1514764800 => 1,],
                        P_03MIN => [1514764800 => 1,],
                        P_05MIN => [1514764800 => 1,],
                        P_15MIN => [1514764800 => 1,],
                        P_30MIN => [1514764800 => 1,],
                        P_45MIN => [1514764800 => 1,],
                        P_01HOUR => [1514764800 => 1,],
                        P_02HOUR => [1514764800 => 1,],
                        P_03HOUR => [1514764800 => 1,],
                        P_04HOUR => [1514764800 => 1,],
                        P_01DAY => [1514764800 => 1,],
                    ],
            ],
        ];
        $this->assertEquals($expected, $this->counter->getRawData());
        $expected = [
            STR_TOTAL => [
                P_01MIN => 0.052,
                P_03MIN => 0.052,
                P_05MIN => 0.052,
                P_15MIN => 0.052,
                P_30MIN => 0.052,
                P_45MIN => 0.052,
                P_01HOUR => 0.052,
                P_02HOUR => 0.052,
                P_03HOUR => 0.052,
                P_04HOUR => 0.052,
                P_01DAY => 0.052,
            ],
            STR_SELL => [
                P_01MIN => 0.012,
                P_03MIN => 0.012,
                P_05MIN => 0.012,
                P_15MIN => 0.012,
                P_30MIN => 0.012,
                P_45MIN => 0.012,
                P_01HOUR => 0.012,
                P_02HOUR => 0.012,
                P_03HOUR => 0.012,
                P_04HOUR => 0.012,
                P_01DAY => 0.012,
            ],
            STR_BUY => [
                P_01MIN => 0.04,
                P_03MIN => 0.04,
                P_05MIN => 0.04,
                P_15MIN => 0.04,
                P_30MIN => 0.04,
                P_45MIN => 0.04,
                P_01HOUR => 0.04,
                P_02HOUR => 0.04,
                P_03HOUR => 0.04,
                P_04HOUR => 0.04,
                P_01DAY => 0.04,
            ],
            STR_TOTAL_VP =>
                [
                    P_01MIN => 520.0,
                    P_03MIN => 520.0,
                    P_05MIN => 520.0,
                    P_15MIN => 520.0,
                    P_30MIN => 520.0,
                    P_45MIN => 520.0,
                    P_01HOUR => 520.0,
                    P_02HOUR => 520.0,
                    P_03HOUR => 520.0,
                    P_04HOUR => 520.0,
                    P_01DAY => 520.0,
                ],
            STR_SELL_VP =>
                [
                    P_01MIN => 120.0,
                    P_03MIN => 120.0,
                    P_05MIN => 120.0,
                    P_15MIN => 120.0,
                    P_30MIN => 120.0,
                    P_45MIN => 120.0,
                    P_01HOUR => 120.0,
                    P_02HOUR => 120.0,
                    P_03HOUR => 120.0,
                    P_04HOUR => 120.0,
                    P_01DAY => 120.0,
                ],
            STR_BUY_VP =>
                [
                    P_01MIN => 400.0,
                    P_03MIN => 400.0,
                    P_05MIN => 400.0,
                    P_15MIN => 400.0,
                    P_30MIN => 400.0,
                    P_45MIN => 400.0,
                    P_01HOUR => 400.0,
                    P_02HOUR => 400.0,
                    P_03HOUR => 400.0,
                    P_04HOUR => 400.0,
                    P_01DAY => 400.0,
                ],
            STR_VWAP_TOTAL =>
                [
                    P_01MIN => 10000,
                    P_03MIN => 10000,
                    P_05MIN => 10000,
                    P_15MIN => 10000,
                    P_30MIN => 10000,
                    P_45MIN => 10000,
                    P_01HOUR => 10000,
                    P_02HOUR => 10000,
                    P_03HOUR => 10000,
                    P_04HOUR => 10000,
                    P_01DAY => 10000,
                ],
            STR_VWAP_SELL =>
                [
                    P_01MIN => 10000,
                    P_03MIN => 10000,
                    P_05MIN => 10000,
                    P_15MIN => 10000,
                    P_30MIN => 10000,
                    P_45MIN => 10000,
                    P_01HOUR => 10000,
                    P_02HOUR => 10000,
                    P_03HOUR => 10000,
                    P_04HOUR => 10000,
                    P_01DAY => 10000,
                ],
            STR_VWAP_BUY =>
                [
                    P_01MIN => 10000,
                    P_03MIN => 10000,
                    P_05MIN => 10000,
                    P_15MIN => 10000,
                    P_30MIN => 10000,
                    P_45MIN => 10000,
                    P_01HOUR => 10000,
                    P_02HOUR => 10000,
                    P_03HOUR => 10000,
                    P_04HOUR => 10000,
                    P_01DAY => 10000,
                ],

        ];
        $this->assertEquals($expected, $this->counter->getVolumeArray());
        $expected = [
            STR_TOTAL => [
                P_01MIN => 3,
                P_03MIN => 3,
                P_05MIN => 3,
                P_15MIN => 3,
                P_30MIN => 3,
                P_45MIN => 3,
                P_01HOUR => 3,
                P_02HOUR => 3,
                P_03HOUR => 3,
                P_04HOUR => 3,
                P_01DAY => 3,
            ],
            STR_SELL => [
                P_01MIN => 2,
                P_03MIN => 2,
                P_05MIN => 2,
                P_15MIN => 2,
                P_30MIN => 2,
                P_45MIN => 2,
                P_01HOUR => 2,
                P_02HOUR => 2,
                P_03HOUR => 2,
                P_04HOUR => 2,
                P_01DAY => 2,
            ],
            STR_BUY => [
                P_01MIN => 1,
                P_03MIN => 1,
                P_05MIN => 1,
                P_15MIN => 1,
                P_30MIN => 1,
                P_45MIN => 1,
                P_01HOUR => 1,
                P_02HOUR => 1,
                P_03HOUR => 1,
                P_04HOUR => 1,
                P_01DAY => 1,
            ],
        ];
        $this->assertEquals($expected, $this->counter->getEventsArray());
    }

    /** @test */
    public function instanceComplexFill(): void
    {
        $data = new Rewindable(
            [CommonTrades::class, 'generator'],
            new Circular([T_ASK, T_BID]),
            new Circular([static::$pair]),
            new Circular(
                [10000,]
            ),
            new Circular([0.001,])
        );

        $this->counter = new VolumeCounter();
        $this->counter->enableRelativeMode();
        foreach ($data as $trade) {
            $this->counter->addTrade($trade);
        }
        dump($this->counter->getRawData());
        dump($this->counter->getVolumeArray());
        $this->assertTrue(true);
    }


    protected function tearDown()
    {
        unset($this->counter);
    }
}
