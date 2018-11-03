<?php
/**
 * User: alec
 * Date: 31.10.18
 * Time: 16:33
 */

namespace Unit;


use AlecRabbit\DataOHLCV;
use PHPUnit\Framework\TestCase;

class DataOHLCVPartialTest extends TestCase
{
    protected static $fp;

    /** @var DataOHLCV */
    protected $ohlcv;

    public static function setUpBeforeClass()
    {
        self::$fp = fopen(__DIR__ . '/../../resources/data.csv', 'rb');
    }

    public static function tearDownAfterClass()
    {
        fclose(self::$fp);
    }

    /**
     * @test
     */
    public function addsDataNormallyFromArray(): void
    {
        $this->ohlcv = new DataOHLCV('btc_usd', 500);
        foreach ($this->simpleData() as $item) {
            [$timestamp, $type, $price, $amount] = $item;
            $this->ohlcv->addTrade($timestamp, $type, $price, $amount);
        }
//        dump($this->ohlcv);
        $expected = [
            1512570360,
            1512571380,
            1512572340,
            1512573360,
            1512574380,
            1512575340,
            1512576360,
            1512577380,
            1512578340,
        ];
        $this->assertEquals($expected, $this->ohlcv->getTimestamps(RESOLUTION_01min));
        $expected = [
            1512570240,
            1512571320,
            1512572220,
            1512573300,
            1512574380,
            1512575280,
            1512576360,
            1512577260,
            1512578340,
        ];
//        dump($this->ohlcv->getTimestamps(RESOLUTION_03min));
        $this->assertEquals($expected, $this->ohlcv->getTimestamps(RESOLUTION_03min));
    }

    public function simpleData(): array
    {
        return [
            [1512570380, 'bid', 12820.7, 0.0538594],
            [1512571380, 'bid', 12820.7, 0.00596801],
            [1512572380, 'bid', 12821.3, 0.19551],
            [1512573380, 'bid', 12807.6, 0.0464246],
            [1512574380, 'bid', 12793, 0.0538914],
            [1512575380, 'ask', 12792, 0.0475634],
            [1512576380, 'bid', 12793, 0.0499],
            [1512577380, 'bid', 12810.2, 0.171179],
            [1512578380, 'bid', 12810.2, 0.0223277],
            [1512579380, 'bid', 12818.5, 0.00210018],
        ];
    }

    /**
     * @test
     */
    public function addsDataNormallyFromFile(): void
    {
        $this->ohlcv = new DataOHLCV('btc_usd', 1440);
        foreach ($this->fileData() as $item) {
            [$timestamp, $type, $price, $amount] = $item;
            $this->ohlcv->addTrade($timestamp, $type, $price, $amount);
        }
        $expected = array_fill(0,23, 0.06666);
        $actual = $this->ohlcv->getVolumes(RESOLUTION_01hour);
        $this->assertEquals($expected, $actual);
//        $this->ohlcv->dump();
//        dump($actual);
    }

    public function fileData(): ?\Generator
    {
        // Read in some d from a CSV file
        while ($d = fgetcsv(self::$fp, 1000)) {
            yield [$d[1], $d[5], $d[2], $d[3]];
        }
    }

}