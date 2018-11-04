<?php
/**
 * User: alec
 * Date: 04.11.18
 * Time: 13:05
 */

namespace Unit\DataProviders;


use AlecRabbit\Circular;

class OHLCBasicDataProvider
{
    protected static $timestamp = 1514764800;
    protected static $step = 15;
    protected static $lines = 5760;
    /** @var Circular */
    protected static $price;
    /** @var Circular */
    protected static $type;
    /** @var Circular */
    protected static $amount;
    private static $init = false;

    public static function data(): ?\Generator
    {
        if (!static::$init) {
            static::init();
        }
        while (static::$lines-- > 0) {
            // [$timestamp, $type, $price, $amount] = $item;
            yield
            [
                static::$timestamp,
                static::$type->getElement(),
                static::$price->getElement(),
                static::$amount->getElement(),
            ];
            static::$timestamp += static::$step;
        }
    }

    protected static function init(): void
    {
        static::$price =
            new Circular(
                [10000.0022, 10000.0001, 10000.2022, 10000.0505]
            );
        static::$type = new Circular(['ask', 'bid']);
        static::$amount = new Circular([0.001, 0.0001, 0.00001, 0.000001]);
        static::$init = true;
    }
}