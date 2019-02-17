<?php
/**
 * Date: 12.11.18
 * Time: 16:17
 */

namespace Tests\Unit\Helpers;


use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * @test
     * @dataProvider cAvgDataProvider
     * @param $expected
     * @param $first
     * @param $second
     */
    public function FunctionCAvg($expected, $first, $second): void
    {
        $this->assertEquals($expected, c_avg($first, $second));
    }

    public function cAvgDataProvider(): array
    {
        return [
            // [$expected, $first, $second],
            [1, 1, 1],
            [0.1, 0.1, 0.1],
            [0.0001, 0.0001, 0.0001],
            [0.00000001, 0.00000001, 0.00000001],
            [2, 4, 0],
        ];
    }

    /**
     * @test
     * @dataProvider cArrayDivDataProvider
     * @param $expected
     * @param $argument
     * @param $divider
     */
    public function FunctionCArrayDiv($expected, $argument, $divider): void
    {
        $this->assertEquals($expected, c_array_div($argument, $divider));
    }

    public function cArrayDivDataProvider(): array
    {
        return [
            // [$expected, $argument, $divider],
            [[1], [1], 1],
            [[1, 2, 3, 4, 5, 6], [1, 2, 3, 4, 5, 6], 1],
            [[1, 2, 3, 4, 5, 6], [2, 4, 6, 8, 10, 12], 2],
            [[4, 8, 12, 16, 20, 24], [2, 4, 6, 8, 10, 12], 0.5],
            [[2], [4], 2],
        ];
    }


    /**
     * @test
     * @dataProvider cArrayAlignedSubDataProvider
     * @param $expected
     * @param $first
     * @param $second
     */
    public function FunctionCArrayAlignedSub($expected, $first, $second): void
    {
        $this->assertEquals($expected, c_array_aligned_sub($first, $second));
    }

    public function cArrayAlignedSubDataProvider(): array
    {
        return [
            // [$expected, $first, $second],
            [[], [], []],
            [
                [
                    30 => 3,
                    40 => 3,
                    50 => 3,
                    60 => 3,
                ],
                [
                    10 => 4,
                    20 => 4,
                    30 => 4,
                    40 => 4,
                    50 => 4,
                    60 => 4,
                ],
                [
                    30 => 1,
                    40 => 1,
                    50 => 1,
                    60 => 1,
                    70 => 1,
                    80 => 1,
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider heartbeatDataProvider
     * @param $expected
     */
    public function heartbeat($expected): void
    {
        $this->assertEquals($expected, heartbeat());
    }

    public function heartbeatDataProvider(): array
    {
        return [
            // [$expected],
            [HEARTBEAT_0],
            [HEARTBEAT_1],
            [HEARTBEAT_2],
            [HEARTBEAT_3],
            [HEARTBEAT_0],
            [HEARTBEAT_1],
            [HEARTBEAT_2],
            [HEARTBEAT_3],
            [HEARTBEAT_0],
            [HEARTBEAT_1],
            [HEARTBEAT_2],
            [HEARTBEAT_3],
        ];
    }

}