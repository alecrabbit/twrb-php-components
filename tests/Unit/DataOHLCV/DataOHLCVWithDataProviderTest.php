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
        foreach ($this->dataToCheckLastHigh() as $resolution => $expected) {
            $this->assertEquals($expected, $this->ohlcv->getLastHigh($resolution), "Resolution: {$resolution}");
        }
        foreach ($this->dataToCheckLastLow() as $resolution => $expected) {
            $this->assertEquals($expected, $this->ohlcv->getLastLow($resolution), "Resolution: {$resolution}");
        }
        foreach ($this->dataToCheckLastClose() as $resolution => $expected) {
            $this->assertEquals($expected, $this->ohlcv->getLastClose($resolution), "Resolution: {$resolution}");
        }
    }

    private function dataToCheckVolumes(): array
    {
        return [
            RESOLUTION_01MIN => array_fill(0, 1439, 0.001111),
            RESOLUTION_03MIN => array_fill(0, 479, 0.003333),
            RESOLUTION_05MIN => array_fill(0, 287, 0.005555),
            RESOLUTION_15MIN => array_fill(0, 95, 0.016665),
            RESOLUTION_30MIN => array_fill(0, 47, 0.03333),
            RESOLUTION_45MIN => array_fill(0, 31, 0.049995),
            RESOLUTION_01HOUR => array_fill(0, 23, 0.06666),
            RESOLUTION_02HOUR => array_fill(0, 11, 0.13332),
            RESOLUTION_03HOUR => array_fill(0, 7, 0.19998),
            RESOLUTION_04HOUR => array_fill(0, 5, 0.26664),
            RESOLUTION_01DAY => array_fill(0, 0, 1.59984),
        ];
    }

    private function dataToCheckOpens(): array
    {
        return [
            RESOLUTION_01MIN => array_fill(0, 1439, 10000.0022),
            RESOLUTION_03MIN => array_fill(0, 479, 10000.0022),
            RESOLUTION_05MIN => array_fill(0, 287, 10000.0022),
            RESOLUTION_15MIN => array_fill(0, 95, 10000.0022),
            RESOLUTION_30MIN => array_fill(0, 47, 10000.0022),
            RESOLUTION_45MIN => array_fill(0, 31, 10000.0022),
            RESOLUTION_01HOUR => array_fill(0, 23, 10000.0022),
            RESOLUTION_02HOUR => array_fill(0, 11, 10000.0022),
            RESOLUTION_03HOUR => array_fill(0, 7, 10000.0022),
            RESOLUTION_04HOUR => array_fill(0, 5, 10000.0022),
            RESOLUTION_01DAY => array_fill(0, 0, 10000.0022),
        ];
    }

    private function dataToCheckHighs(): array
    {
        return [
            RESOLUTION_01MIN => array_fill(0, 1439, 10000.2022),
            RESOLUTION_03MIN => array_fill(0, 479, 10000.2022),
            RESOLUTION_05MIN => array_fill(0, 287, 10000.2022),
            RESOLUTION_15MIN => array_fill(0, 95, 10000.2022),
            RESOLUTION_30MIN => array_fill(0, 47, 10000.2022),
            RESOLUTION_45MIN => array_fill(0, 31, 10000.2022),
            RESOLUTION_01HOUR => array_fill(0, 23, 10000.2022),
            RESOLUTION_02HOUR => array_fill(0, 11, 10000.2022),
            RESOLUTION_03HOUR => array_fill(0, 7, 10000.2022),
            RESOLUTION_04HOUR => array_fill(0, 5, 10000.2022),
            RESOLUTION_01DAY => array_fill(0, 0, 10000.2022),
        ];
    }

    private function dataToCheckLows(): array
    {
        return [
            RESOLUTION_01MIN => array_fill(0, 1439, 10000.0001),
            RESOLUTION_03MIN => array_fill(0, 479, 10000.0001),
            RESOLUTION_05MIN => array_fill(0, 287, 10000.0001),
            RESOLUTION_15MIN => array_fill(0, 95, 10000.0001),
            RESOLUTION_30MIN => array_fill(0, 47, 10000.0001),
            RESOLUTION_45MIN => array_fill(0, 31, 10000.0001),
            RESOLUTION_01HOUR => array_fill(0, 23, 10000.0001),
            RESOLUTION_02HOUR => array_fill(0, 11, 10000.0001),
            RESOLUTION_03HOUR => array_fill(0, 7, 10000.0001),
            RESOLUTION_04HOUR => array_fill(0, 5, 10000.0001),
            RESOLUTION_01DAY => array_fill(0, 0, 10000.0001),
        ];
    }

    private function dataToCheckCloses(): array
    {
        return [
            RESOLUTION_01MIN => array_fill(0, 1439, 10000.0505),
            RESOLUTION_03MIN => array_fill(0, 479, 10000.0505),
            RESOLUTION_05MIN => array_fill(0, 287, 10000.0505),
            RESOLUTION_15MIN => array_fill(0, 95, 10000.0505),
            RESOLUTION_30MIN => array_fill(0, 47, 10000.0505),
            RESOLUTION_45MIN => array_fill(0, 31, 10000.0505),
            RESOLUTION_01HOUR => array_fill(0, 23, 10000.0505),
            RESOLUTION_02HOUR => array_fill(0, 11, 10000.0505),
            RESOLUTION_03HOUR => array_fill(0, 7, 10000.0505),
            RESOLUTION_04HOUR => array_fill(0, 5, 10000.0505),
            RESOLUTION_01DAY => array_fill(0, 0, 10000.0505),
        ];
    }

    private function dataToCheckHasPeriods(): array
    {
        return [
            RESOLUTION_01MIN => true,
            RESOLUTION_03MIN => true,
            RESOLUTION_05MIN => true,
            RESOLUTION_15MIN => true,
            RESOLUTION_30MIN => true,
            RESOLUTION_45MIN => true,
            RESOLUTION_01HOUR => true,
            RESOLUTION_02HOUR => true,
            RESOLUTION_03HOUR => true,
            RESOLUTION_04HOUR => true,
            RESOLUTION_01DAY => false,
        ];
    }

    private function dataToCheckLastHigh(): array
    {
        return [
            RESOLUTION_01MIN => 10000.2022,
            RESOLUTION_03MIN => 10000.2022,
            RESOLUTION_05MIN => 10000.2022,
            RESOLUTION_15MIN => 10000.2022,
            RESOLUTION_30MIN => 10000.2022,
            RESOLUTION_45MIN => 10000.2022,
            RESOLUTION_01HOUR => 10000.2022,
            RESOLUTION_02HOUR => 10000.2022,
            RESOLUTION_03HOUR => 10000.2022,
            RESOLUTION_04HOUR => 10000.2022,
            RESOLUTION_01DAY => null,
        ];
    }

    private function dataToCheckLastLow(): array
    {
        return [
            RESOLUTION_01MIN => 10000.0001,
            RESOLUTION_03MIN => 10000.0001,
            RESOLUTION_05MIN => 10000.0001,
            RESOLUTION_15MIN => 10000.0001,
            RESOLUTION_30MIN => 10000.0001,
            RESOLUTION_45MIN => 10000.0001,
            RESOLUTION_01HOUR => 10000.0001,
            RESOLUTION_02HOUR => 10000.0001,
            RESOLUTION_03HOUR => 10000.0001,
            RESOLUTION_04HOUR => 10000.0001,
            RESOLUTION_01DAY => null,
        ];
    }

    private function dataToCheckLastClose(): array
    {
        return [
            RESOLUTION_01MIN => 10000.0505,
            RESOLUTION_03MIN => 10000.0505,
            RESOLUTION_05MIN => 10000.0505,
            RESOLUTION_15MIN => 10000.0505,
            RESOLUTION_30MIN => 10000.0505,
            RESOLUTION_45MIN => 10000.0505,
            RESOLUTION_01HOUR => 10000.0505,
            RESOLUTION_02HOUR => 10000.0505,
            RESOLUTION_03HOUR => 10000.0505,
            RESOLUTION_04HOUR => 10000.0505,
            RESOLUTION_01DAY => null,
        ];
    }

    protected function tearDown()
    {
        unset($this->ohlcv);
    }

}