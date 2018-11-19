<?php
/**
 * User: alec
 * Date: 31.10.18
 * Time: 16:33
 */

namespace Unit;


use AlecRabbit\DataOHLCV;
use PHPUnit\Framework\TestCase;

class DataOHLCVSimpleUnsortedTest extends TestCase
{
    /** @var DataOHLCV */
    protected $ohlcv;

    /**
     * @test
     */
    public function simpleDataCheck(): void
    {
        $this->ohlcv = new DataOHLCV('btc_usd', 500);

        $this->expectException(\RuntimeException::class);
        foreach ($this->simpleData() as $item) {
            [$timestamp, $type, $price, $amount] = $item;
            $this->ohlcv->addTrade($timestamp, $type, $price, $amount);
        }
    }

    public function simpleData(): array
    {
        return [
            [1512570380, T_BID, 12820.7, 0.0538594],
            [1512578380, T_BID, 12810.2, 0.0223277],
            [1512571380, T_BID, 12820.7, 0.00596801],
            [1512572380, T_BID, 12821.3, 0.19551],
            [1512573380, T_BID, 12807.6, 0.0464246],
            [1512574380, T_BID, 12793, 0.0538914],
            [1512575380, T_ASK, 12792, 0.0475634],
            [1512576380, T_BID, 12793, 0.0499],
            [1512577380, T_BID, 12810.2, 0.171179],
            [1512579380, T_BID, 12818.5, 0.00210018],
            [1512585380, T_ASK, 12792, 0.0475634],
            [1512586380, T_BID, 12793, 0.0499],
            [1512587380, T_BID, 12810.2, 0.171179],
            [1512588380, T_BID, 12810.2, 0.0223277],
            [1512589380, T_BID, 12818.5, 0.00210018],
            [1513589380, T_BID, 12818.5, 0.00210018],
        ];
    }
    /**
     * @test
     */
    public function simpleDataTrimCheck(): void
    {
        $this->ohlcv = new DataOHLCV('btc_usd', 10);

        foreach ($this->simpleTrimData() as $item) {
            [$timestamp, $type, $price, $amount] = $item;
            $this->ohlcv->addTrade($timestamp, $type, $price, $amount);
        }
        $this->assertEquals(10, $this->ohlcv->getSize());
    }

    public function simpleTrimData(): \Generator
    {
        $n = 1000;
        $timestamp = 1512570380;
        for($i = 0; $i< $n; $i++) {
            yield [$timestamp, T_BID, 10000.0, 0.0001];
            $timestamp += 6400;
        }
    }

}