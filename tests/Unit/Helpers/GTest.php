<?php
/**
 * User: alec
 * Date: 27.11.18
 * Time: 17:58
 */

namespace Tests\Unit\Helpers;

use AlecRabbit\Helpers\G;
use PHPUnit\Framework\TestCase;

class GTest extends TestCase
{

    /**
     * @test
     * @dataProvider rangeDataProvider
     * @param $expected
     * @param $start
     * @param $stop
     * @param $step
     */
    public function range($expected, $start, $stop, $step): void
    {
        $result = [];
        foreach (G::range($start, $stop, $step) as $value) {
            $result[] = $value;
        }
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @dataProvider rangeDataProvider
     * @param $expected
     * @param $start
     * @param $stop
     * @param $step
     */
    public function rangeRewindable($expected, $start, $stop, $step): void
    {
        $range = G::rewindableRange($start, $stop, $step);
        $this->assertEquals($expected, iterator_to_array($range));
        $this->assertEquals($expected, iterator_to_array($range));
    }

    public function rangeDataProvider(): array
    {
        return [
            // [$expected, $start, $stop, $step],
            [[1, 2, 3, 4, 5], 1, 5, 1],
            [[5, 4, 3, 2, 1], 5, 1, 1],
            [[-2, -1, 0, 1, 2], -2, 2, 1],
            [[-3, -1, 1,], -3, 2, 2],
            [[1,], 1, 1, 2],
            [[1,], 1, 1, 1],
            [[-1,], -1, -1, 1],
            [[-1,], -1, -1, 2],
        ];
    }
}