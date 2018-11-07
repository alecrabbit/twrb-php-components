<?php
/**
 * User: alec
 * Date: 07.11.18
 * Time: 15:36
 */

namespace Unit\DataProviders;


use AlecRabbit\Money\Money;

class MoneyBasicDataProvider
{
    public static function equalityDataProvider(): array
    {
        return [
            // [$amount, $currency, $resulted_amount, $resulted_currency, $equality],
            [null, 'eUr', '0', 'EUR', true],
            [6, 'usdEt', '5', 'USDET', false],
            [5.2, 'eUr', '5.2', 'EUR', true],
            [-0.1232, 'usd', '-0.1232', 'USD', true],
            [-0.1232, 'usd', null, 'USD', false],
            [5, 'usd', '5', 'USD', true],
            [5, 'usdEt', '5', 'USDET', true],
            [5, 'usdEt', '5', 'eur', false],

        ];
    }

    public static function divideDataProvider(): array
    {
        return [
//            [$result, $amount, $divisor],
            ['1', 1, 1],
            ['1.24124', 1.24124, 1],
            ['0.5', 3, 6],
            ['0.68492305413977', 2.22, 3.24124],
            ['0.68940613061185', 2.23453462623452, 3.24124565624525],
        ];
    }

    public static function comparisonDataProvider(): array
    {
        return [
            // [$expected, $amount, $currency, $second_amount]
            [0, 1, 'eur', 1],
            [1, 2, 'eur', 1],
            [-1, 0, 'eur', 1],
        ];
    }

    public static function invalidOperandDataProvider(): array
    {
        return [
            [[]],
            [false],
            ['operand'],
            [null],
            [new \stdClass()],
        ];
    }

    public static function allocationDataProvider(): array
    {
        return [
            [32453.34, [1, 1, 1], [10818, 10818, 10817.34], 0],
            [32453, [1, 1, 1], [10818, 10818, 10817], 0],
            [32453, [1, 1, 1], [10817.67, 10817.67, 10817.66], null],
            [100, [1, 1, 1], [33.34, 33.33, 33.33], null],
            [100, [1, 1, 1], [34, 33, 33], 0],
            [101, [1, 1, 1], [33.67, 33.67, 33.66], null],
            [-101, [1, 1, 1], [-33.66, -33.67, -33.67], null],
            [5, [3, 7], [2, 3], 0],
            [5, [3, 7], [1.5, 3.5], 2],
            [5, [3, 8], [1.36, 3.64], 2],
            [5, [7, 3], [4, 1], 0],
            [5, [7, 3], [3.5, 1.5], 2],
            [5, [0, 5, 3], [0, 3.13, 1.87], 2],
            [5, [7, 3, 0], [4, 1, 0], 0],
            [5, [7, 3, 0], [3.5, 1.5, 0], 3],
            [6.34, [7, 3, 0], [4.438, 1.902, 0], 5],
            [6.34456456, [5, 3, 1], [3.52475809, 2.11485485, 0.70495162], 8],
            [-5, [7, 3], [-3, -2], 0],
            [-5, [7, 3], [-3.5, -1.5], 2],
            [-2, [3, 5], [-0.75, -1.25], 4],
            [5, [0, 7, 3], [0, 4, 1], 0],
            [5, [7, 0, 3], [4, 0, 1], 0],
            [7, [7, 0, 3], [4.9, 0, 2.1], 2],
            [5, [0, 0, 1], [0, 0, 5], 0],
            [5, [0, 3, 7], [0, 2, 3], 0],
            [0, [0, 0, 1], [0, 0, 0], 0],
            [2, [1, 1, 1], [1, 1, 0], 0],
            [1, [1, 1], [1, 0], 0],
        ];
    }

    public static function allocationTargetDataProvider(): array
    {
        return [
            [15, 2, [7.5, 7.5], null],
            [10, 2, [5, 5], null],
            [15, 3, [5, 5, 5], null],
            [10, 3, [3.34, 3.33, 3.33], null],
            [10, 3, [3.33333334, 3.33333333, 3.33333333], 8],
        ];
    }

    public static function comparatorDataProvider(): array
    {
        return [
            // [$amount, $isZero, $isPositive, $isNegative],
            [1, false, true, false],
            [0, true, false, false],
            [-1, false, false, true],
            ['1', false, true, false],
            ['0', true, false, false],
            ['-1', false, false, true],
        ];
    }

    public static function absoluteDataProvider(): array
    {
        return [
            [1, 1],
            [1, -1],
            [21, -21],
            [2, 2],
            [0, 0],
            [0.234, -0.234],
            ['0.2349', -0.234900000],
        ];
    }

    public static function negativeDataProvider(): array
    {
        return [
            [1, -1],
            [0, 0],
            [-1, 1],
            ['1', -1],
            ['0', 0],
            ['-1', 1],
        ];
    }

    public static function modDataProvider(): array
    {
        return [
            [11, 5, '1'],
            [9, 3, '0'],
            [1006, 10, '6'],
            [1007, 10, '7'],
        ];
    }

    public static function sumDataProvider(): array
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15)], Money::EUR(30.000)],
            [[Money::EUR(-5), Money::EUR(-10.000), Money::EUR(-15)], Money::EUR(-30)],
            [[Money::EUR(-5), Money::EUR(10.000), Money::EUR(-15)], Money::EUR(-10.0)],
            [[Money::EUR(0)], Money::EUR(0)],
            [[Money::EUR(0), Money::EUR(0), Money::EUR(0)], Money::EUR(0)],
            [[Money::EUR(0), Money::EUR(0.001), Money::EUR(0)], Money::EUR('0.00100')],
        ];
    }

    public static function minDataProvider(): array
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15.0032323000)], Money::EUR(5)],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-15)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }

    public static function maxDataProvider(): array
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15.0032323000)], Money::EUR('15.0032323')],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-5)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }

    public static function avgDataProvider(): array
    {
        return [
            [[Money::EUR(5), Money::EUR(1.5077), Money::EUR(8.2), Money::EUR(10), Money::EUR(15)], Money::EUR(7.94154)],
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15)], Money::EUR(10)],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-10)],
            [[Money::EUR(0)], Money::EUR(0)],
            [[Money::EUR(1)], Money::EUR(1)],
        ];
    }

    public static function multiplyDataProvider(): array
    {
        return [
            // [$result, $amount, $multiplier],
            ['1', 1, 1],
            ['1.24124', 1, 1.24124],
            ['7.1955528', 2.22, 3.24124],
            ['7.24267565101206', 2.23453462623452, 3.24124565624525],
        ];
    }

    public static function newInstanceDataProvider(): array
    {
        return [
            // [$amount, $currency, $resulted_amount, $resulted_currency],
            [null, 'eUr', '0', 'EUR'],
            [5.2, 'eUr', '5.2', 'EUR'],
            [-0.1232, 'usd', '-0.1232', 'USD'],
            [5, 'usd', '5', 'USD'],
            [5, 'usdEt', '5', 'USDET'],
        ];
    }

    public function newInstanceBadDataProvider(): array
    {
        return [
            // [$amount, $currency],
            [false, 'eUr'],
            [new \stdClass(), 'eUr'],
            [null, true],
            [1, 'CODETOOLONG'],
        ];
    }
}