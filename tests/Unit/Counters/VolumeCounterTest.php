<?php
/**
 * User: alec
 * Date: 19.11.18
 * Time: 20:06
 */

namespace Tests\Unit\Counters;

use AlecRabbit\Circular;
use AlecRabbit\Counters\VolumeCounterDeprecated;
use AlecRabbit\Rewindable;
use AlecRabbit\Structures\Trade;
use AlecRabbit\Structures\Volume;
use PHPUnit\Framework\TestCase;
use Tests\Unit\DataProviders\CommonTrades;

class VolumeCounterTest extends TestCase
{
    /** @var Rewindable */
    private static $data;

    /** @var VolumeCounterDeprecated */
    private $counter;

    public static function setUpBeforeClass()
    {
        $pair = 'btc_usd';
        static::$data = new Rewindable(
            [CommonTrades::class, 'generator'],
            new Circular([T_ASK, T_BID]),
            new Circular([$pair]),
            new Circular(
                [10000.0022, 10000.0001, 10000.2022, 10000.0505]
            ),
            new Circular([0.001, 0.002, 0.002, 0.001])
        );
    }

    /** @test */
    public function instance(): void
    {
        $this->counter = new VolumeCounterDeprecated();
        $this->assertInstanceOf(VolumeCounterDeprecated::class, $this->counter);
        $expected =
            (new Volume())
                ->setTotal(0)
                ->setSell(0)
                ->setBuy(0);
        $this->assertEquals($expected, $this->counter->getCalculatedVolumes());
    }

    /** @test */
    public function getVolumes(): void
    {
        $this->counter = new VolumeCounterDeprecated();
        $this->counter->enableRelativeMode();
        $expected =
            [[], [], []];
        $this->assertEquals($expected, $this->counter->getVolumes());
        $this->counter->addTrade(
            new Trade(T_SELL, 'btc_usd', 10000, 0.001, 1542739823)
        );
        $expected =
            [[1542739823 => 0.001], [1542739823 => 0.001], []];
        $this->assertEquals($expected, $this->counter->getVolumes());
        $this->counter->addTrade(
            new Trade(T_BUY, 'btc_usd', 10000, 0.001, 1542739823)
        );
        $expected =
            [[1542739823 => 0.002], [1542739823 => 0.001], [1542739823 => 0.001]];
        $this->assertEquals($expected, $this->counter->getVolumes());
    }

    /** @test */
    public function getVolumesInCurrentTIme(): void
    {
        $this->counter = new VolumeCounterDeprecated();
        $this->counter->disableRelativeMode();
        $timestamp = time();
        $expected =
            [[], [], []];
        $this->assertEquals($expected, $this->counter->getVolumes());
        $this->counter->addTrade(
            new Trade(T_SELL, 'btc_usd', 10000, 0.001, $timestamp)
        );
        $expected =
            [[$timestamp => 0.001], [$timestamp => 0.001], []];
        $this->assertEquals($expected, $this->counter->getVolumes());
        $this->counter->addTrade(
            new Trade(T_BUY, 'btc_usd', 10000, 0.001, $timestamp)
        );
        $expected =
            [[$timestamp => 0.002], [$timestamp => 0.001], [$timestamp => 0.001]];
        $this->assertEquals($expected, $this->counter->getVolumes());
    }

    /** @test */
    public function getVolumesGrouped(): void
    {
        $this->counter = new VolumeCounterDeprecated(null, P_01MIN);
        $this->counter->enableRelativeMode();
        $expected =
            [[], [], []];
        $this->assertEquals($expected, $this->counter->getVolumes());
        $this->counter->addTrade(
            new Trade(T_SELL, 'btc_usd', 10000, 0.001, 1542739823)
        );
        $this->counter->addTrade(
            new Trade(T_SELL, 'btc_usd', 10000, 0.001, 1542739893)
        );
        $expected =
            [[1542739800 => 0.001, 1542739860 => 0.001], [1542739800 => 0.001, 1542739860 => 0.001], []];
        $this->assertEquals($expected, $this->counter->getVolumes());
        $this->counter->addTrade(
            new Trade(T_BUY, 'btc_usd', 10000, 0.001, 1542739823)
        );
        $this->counter->addTrade(
            new Trade(T_BUY, 'btc_usd', 10000, 0.001, 1542739893)
        );
        $expected =
            [
                [1542739800 => 0.002, 1542739860 => 0.002],
                [1542739800 => 0.001, 1542739860 => 0.001],
                [1542739800 => 0.001, 1542739860 => 0.001]
            ];
        $this->assertEquals($expected, $this->counter->getVolumes());
    }

    /**
     * @test
     * @dataProvider dataForFillByProvider
     * @param $expected
     * @param $length
     * @param $groupBy
     */
    public function fillByProvider($expected, $length, $groupBy): void
    {
        $this->counter = new VolumeCounterDeprecated($length, $groupBy);
        $this->counter->setRelativeMode();

        foreach (static::$data as $trade) {
            $this->counter->addTrade($trade);
        }
        $this->assertEquals($expected, $this->counter->getCalculatedVolumes(true));
        $expected =
            (new Volume())
                ->setTotal(0)
                ->setSell(0)
                ->setBuy(0);
        $this->assertEquals($expected, $this->counter->getCalculatedVolumes());
    }

    public function dataForFillByProvider(): array
    {
        return
            [
                [
                    (new Volume())->setTotal(0.006)->setBuy(0.003)->setSell(0.003),
                    P_01MIN,
                    ONE_SECOND
                ],
                [
                    (new Volume())->setTotal(0.018)->setBuy(0.009)->setSell(0.009),
                    P_03MIN,
                    ONE_SECOND
                ],
                [
                    (new Volume())->setTotal(0.030)->setBuy(0.015)->setSell(0.015),
                    P_05MIN,
                    ONE_SECOND
                ],
                [
                    (new Volume())->setTotal(0.090)->setBuy(0.045)->setSell(0.045),
                    P_15MIN,
                    ONE_SECOND
                ],
                [
                    (new Volume())->setTotal(0.18)->setBuy(0.09)->setSell(0.09),
                    P_30MIN,
                    ONE_SECOND
                ],
                [
                    (new Volume())->setTotal(0.36)->setBuy(0.18)->setSell(0.18),
                    P_01HOUR,
                    P_01MIN
                ],
                [
                    (new Volume())->setTotal(0.72)->setBuy(0.36)->setSell(0.36),
                    P_02HOUR,
                    P_01MIN
                ],
                [
                    (new Volume())->setTotal(1.08)->setBuy(0.54)->setSell(0.54),
                    P_03HOUR,
                    P_01MIN
                ],
                [
                    (new Volume())->setTotal(1.44)->setBuy(0.72)->setSell(0.72),
                    P_04HOUR,
                    P_05MIN
                ],
                [
                    (new Volume())->setTotal(8.64)->setBuy(4.32)->setSell(4.32),
                    P_01DAY,
                    P_01HOUR
                ],
            ];
    }

    protected function tearDown()
    {
        unset($this->counter);
    }
}
