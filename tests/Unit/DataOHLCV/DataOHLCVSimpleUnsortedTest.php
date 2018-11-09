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

    public function setUp()
    {
        $this->ohlcv = new DataOHLCV('btc_usd', 500);
    }
    /**
     * @test
     */
    public function simpleDataCheck(): void
    {
        $this->expectException(\RuntimeException::class);
        foreach ($this->simpleData() as $item) {
            [$timestamp, $type, $price, $amount] = $item;
            $this->ohlcv->addTrade($timestamp, $type, $price, $amount);
        }
    }

    public function simpleData(): array
    {
        return [
            [1512570380, 'bid', 12820.7, 0.0538594],
            [1512578380, 'bid', 12810.2, 0.0223277],
            [1512571380, 'bid', 12820.7, 0.00596801],
            [1512572380, 'bid', 12821.3, 0.19551],
            [1512573380, 'bid', 12807.6, 0.0464246],
            [1512574380, 'bid', 12793, 0.0538914],
            [1512575380, 'ask', 12792, 0.0475634],
            [1512576380, 'bid', 12793, 0.0499],
            [1512577380, 'bid', 12810.2, 0.171179],
            [1512579380, 'bid', 12818.5, 0.00210018],
            [1512585380, 'ask', 12792, 0.0475634],
            [1512586380, 'bid', 12793, 0.0499],
            [1512587380, 'bid', 12810.2, 0.171179],
            [1512588380, 'bid', 12810.2, 0.0223277],
            [1512589380, 'bid', 12818.5, 0.00210018],
            [1513589380, 'bid', 12818.5, 0.00210018],
        ];
    }

}