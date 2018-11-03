<?php
/**
 * User: alec
 * Date: 31.10.18
 * Time: 16:33
 */

namespace Unit;


use AlecRabbit\DataOHLCV;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class DataOHLCVNextResolutionTest extends TestCase
{
    /**
     * @test
     * @dataProvider forNextResolution
     * @param $expected
     * @param $param
     * @throws \ReflectionException
     */
    public function nextResolution($expected, $param): void
    {
        $method = new ReflectionMethod(DataOHLCV::class, 'nextResolution');
        $method->setAccessible(true);

        $object = new DataOHLCV('btc_usd', 500);

        $this->assertEquals($expected, $method->invoke($object, $param));
        unset($method, $object);
    }

    public function forNextResolution(): array
    {
        return [
            [RESOLUTION_03min, RESOLUTION_01min],
            [RESOLUTION_05min, RESOLUTION_03min],
            [RESOLUTION_15min, RESOLUTION_05min],
            [RESOLUTION_30min, RESOLUTION_15min],
            [RESOLUTION_45min, RESOLUTION_30min],
            [RESOLUTION_01hour, RESOLUTION_45min],
            [RESOLUTION_02hour, RESOLUTION_01hour],
            [RESOLUTION_03hour, RESOLUTION_02hour],
            [RESOLUTION_04hour, RESOLUTION_03hour],
            [RESOLUTION_01day, RESOLUTION_04hour],
            [false, RESOLUTION_01day],
        ];
    }


}