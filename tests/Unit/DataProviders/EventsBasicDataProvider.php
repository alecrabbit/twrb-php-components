<?php
/**
 * User: alec
 * Date: 04.11.18
 * Time: 13:05
 */

namespace Unit\DataProviders;


class EventsBasicDataProvider
{
    protected static $timestamp = 1514764800;
    protected static $step = 15;
    protected static $lines = 5760;

    public static function data(): ?\Generator
    {
        while (static::$lines-- > 0) {
            yield static::$timestamp;
            static::$timestamp += static::$step;
        }
    }
}