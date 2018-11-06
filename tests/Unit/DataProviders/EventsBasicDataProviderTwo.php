<?php
/**
 * User: alec
 * Date: 04.11.18
 * Time: 13:05
 */

namespace Unit\DataProviders;


class EventsBasicDataProviderTwo
{
    protected static $timestamp = 1514764800;
    protected static $step = 1;
    protected static $lines = 140;

    public static function data(): ?\Generator
    {
        $number = 1;
        while (static::$lines-- > 0) {
            yield static::$timestamp;
            if ($number % 2 === 0) {
                static::$timestamp += static::$step;
            }
            $number++;
        }
    }
}