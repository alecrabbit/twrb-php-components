<?php
/**
 * User: alec
 * Date: 31.10.18
 * Time: 16:33
 */

namespace Unit;


use AlecRabbit\DataOHLCV;
use PHPUnit\Framework\TestCase;
use Unit\DataProviders\OHLCBasicDataProvider;

class DataOHLCVWithDataProviderTest extends TestCase
{
    /** @var DataOHLCV */
    protected $ohlcv;

    public function setUp()
    {
        $this->ohlcv = new DataOHLCV('btc_usd', 1440);
        foreach (OHLCBasicDataProvider::data() as $item) {
            [$timestamp, $type, $price, $amount] = $item;
            $this->ohlcv->addTrade($timestamp, $type, $price, $amount);
        }
    }

    /**
     * @test
     */
    public function check(): void
    {
        foreach ($this->dataToCheckVolumes() as $resolution => $expected) {
            $this->assertEquals($expected, $this->ohlcv->getVolumes($resolution));
        }
        foreach ($this->dataToCheckOpens() as $resolution => $expected) {
            $this->assertEquals($expected, $this->ohlcv->getOpens($resolution));
        }
        foreach ($this->dataToCheckHighs() as $resolution => $expected) {
            $this->assertEquals($expected, $this->ohlcv->getHighs($resolution));
        }
        foreach ($this->dataToCheckLows() as $resolution => $expected) {
            $this->assertEquals($expected, $this->ohlcv->getLows($resolution));
        }
        foreach ($this->dataToCheckCloses() as $resolution => $expected) {
            $this->assertEquals($expected, $this->ohlcv->getCloses($resolution));
        }
        foreach ($this->dataToCheckHasPeriods() as $resolution => $expected) {
            $this->assertEquals($expected, $this->ohlcv->hasPeriods($resolution, 1));
        }
//        foreach ($this->dataToCheckLastClose() as $resolution => $expected) {
//            $this->assertEquals($expected, $this->ohlcv->getLastClose($resolution));
//        }
    }

    private function dataToCheckVolumes(): array
    {
        return [
            RESOLUTION_01min => array_fill(0, 1439, 0.001111),
            RESOLUTION_03min => array_fill(0, 479, 0.003333),
            RESOLUTION_05min => array_fill(0, 287, 0.005555),
            RESOLUTION_15min => array_fill(0, 95, 0.016665),
            RESOLUTION_30min => array_fill(0, 47, 0.03333),
            RESOLUTION_45min => array_fill(0, 31, 0.049995),
            RESOLUTION_01hour => array_fill(0, 23, 0.06666),
            RESOLUTION_02hour => array_fill(0, 11, 0.13332),
            RESOLUTION_03hour => array_fill(0, 7, 0.19998),
            RESOLUTION_04hour => array_fill(0, 5, 0.26664),
            RESOLUTION_01day => array_fill(0, 0, 1.59984),
        ];
    }

    private function dataToCheckOpens(): array
    {
        return [
            RESOLUTION_01min => array_fill(0, 1439, 10000.0022),
            RESOLUTION_03min => array_fill(0, 479, 10000.0022),
            RESOLUTION_05min => array_fill(0, 287, 10000.0022),
            RESOLUTION_15min => array_fill(0, 95, 10000.0022),
            RESOLUTION_30min => array_fill(0, 47, 10000.0022),
            RESOLUTION_45min => array_fill(0, 31, 10000.0022),
            RESOLUTION_01hour => array_fill(0, 23, 10000.0022),
            RESOLUTION_02hour => array_fill(0, 11, 10000.0022),
            RESOLUTION_03hour => array_fill(0, 7, 10000.0022),
            RESOLUTION_04hour => array_fill(0, 5, 10000.0022),
            RESOLUTION_01day => array_fill(0, 0, 10000.0022),
        ];
    }

    private function dataToCheckHighs(): array
    {
        return [
            RESOLUTION_01min => array_fill(0, 1439, 10000.2022),
            RESOLUTION_03min => array_fill(0, 479, 10000.2022),
            RESOLUTION_05min => array_fill(0, 287, 10000.2022),
            RESOLUTION_15min => array_fill(0, 95, 10000.2022),
            RESOLUTION_30min => array_fill(0, 47, 10000.2022),
            RESOLUTION_45min => array_fill(0, 31, 10000.2022),
            RESOLUTION_01hour => array_fill(0, 23, 10000.2022),
            RESOLUTION_02hour => array_fill(0, 11, 10000.2022),
            RESOLUTION_03hour => array_fill(0, 7, 10000.2022),
            RESOLUTION_04hour => array_fill(0, 5, 10000.2022),
            RESOLUTION_01day => array_fill(0, 0, 10000.2022),
        ];
    }

    private function dataToCheckLows(): array
    {
        return [
            RESOLUTION_01min => array_fill(0, 1439, 10000.0001),
            RESOLUTION_03min => array_fill(0, 479, 10000.0001),
            RESOLUTION_05min => array_fill(0, 287, 10000.0001),
            RESOLUTION_15min => array_fill(0, 95, 10000.0001),
            RESOLUTION_30min => array_fill(0, 47, 10000.0001),
            RESOLUTION_45min => array_fill(0, 31, 10000.0001),
            RESOLUTION_01hour => array_fill(0, 23, 10000.0001),
            RESOLUTION_02hour => array_fill(0, 11, 10000.0001),
            RESOLUTION_03hour => array_fill(0, 7, 10000.0001),
            RESOLUTION_04hour => array_fill(0, 5, 10000.0001),
            RESOLUTION_01day => array_fill(0, 0, 10000.0001),
        ];
    }

    private function dataToCheckCloses(): array
    {
        return [
            RESOLUTION_01min => array_fill(0, 1439, 10000.0505),
            RESOLUTION_03min => array_fill(0, 479, 10000.0505),
            RESOLUTION_05min => array_fill(0, 287, 10000.0505),
            RESOLUTION_15min => array_fill(0, 95, 10000.0505),
            RESOLUTION_30min => array_fill(0, 47, 10000.0505),
            RESOLUTION_45min => array_fill(0, 31, 10000.0505),
            RESOLUTION_01hour => array_fill(0, 23, 10000.0505),
            RESOLUTION_02hour => array_fill(0, 11, 10000.0505),
            RESOLUTION_03hour => array_fill(0, 7, 10000.0505),
            RESOLUTION_04hour => array_fill(0, 5, 10000.0505),
            RESOLUTION_01day => array_fill(0, 0, 10000.0505),
        ];
    }

    private function dataToCheckHasPeriods(): array
    {
        return [
            RESOLUTION_01min => true,
            RESOLUTION_03min => true,
            RESOLUTION_05min => true,
            RESOLUTION_15min => true,
            RESOLUTION_30min => true,
            RESOLUTION_45min => true,
            RESOLUTION_01hour => true,
            RESOLUTION_02hour => true,
            RESOLUTION_03hour => true,
            RESOLUTION_04hour => true,
            RESOLUTION_01day => false,
        ];
    }

    protected function tearDown()
    {
        unset($this->ohlcv);
    }

//    private function dataToCheckLastClose():array
//
//    {
//        return [
//            RESOLUTION_01min => 10000.0505,
//            RESOLUTION_03min => 10000.0505,
//            RESOLUTION_05min => 10000.0505,
//            RESOLUTION_15min => 10000.0505,
//            RESOLUTION_30min => 10000.0505,
//            RESOLUTION_45min => 10000.0505,
//            RESOLUTION_01hour => 10000.0505,
//            RESOLUTION_02hour => 10000.0505,
//            RESOLUTION_03hour => 10000.0505,
//            RESOLUTION_04hour => 10000.0505,
//            RESOLUTION_01day => 10000.0505,
//        ];
//
//    }

}