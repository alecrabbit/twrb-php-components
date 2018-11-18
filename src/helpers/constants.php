<?php
/**
 * User: alec
 * Date: 31.10.18
 * Time: 15:25
 */

// Trade constants
define('T_SELL', 100);
define('T_BUY', -100);

define('T_ASK', T_SELL);
define('T_BID', T_BUY);
define(
    'T_ALIASES',
    [
        'sell'  => T_SELL,
        'buy'  => T_BUY,
        'ask'  => T_ASK,
        'bid'  => T_BID,
    ]
);


define('SECONDS_IN_01MIN', 60);
define('SECONDS_IN_03MIN', 180);
define('SECONDS_IN_05MIN', 300);
define('SECONDS_IN_15MIN', 900);
define('SECONDS_IN_30MIN', 1800);
define('SECONDS_IN_45MIN', 2700);
define('SECONDS_IN_01HOUR', 3600);
define('SECONDS_IN_02HOUR', 7200);
define('SECONDS_IN_03HOUR', 10800);
define('SECONDS_IN_04HOUR', 14400);
define('SECONDS_IN_01DAY', 86400);

define('RESOLUTION_01MIN', SECONDS_IN_01MIN);
define('RESOLUTION_03MIN', SECONDS_IN_03MIN);
define('RESOLUTION_05MIN', SECONDS_IN_05MIN);
define('RESOLUTION_15MIN', SECONDS_IN_15MIN);
define('RESOLUTION_30MIN', SECONDS_IN_30MIN);
define('RESOLUTION_45MIN', SECONDS_IN_45MIN);
define('RESOLUTION_01HOUR', SECONDS_IN_01HOUR);
define('RESOLUTION_02HOUR', SECONDS_IN_02HOUR);
define('RESOLUTION_03HOUR', SECONDS_IN_03HOUR);
define('RESOLUTION_04HOUR', SECONDS_IN_04HOUR);
define('RESOLUTION_01DAY', SECONDS_IN_01DAY);

define(
    'RESOLUTIONS',
    [
        RESOLUTION_01MIN,
        RESOLUTION_03MIN,
        RESOLUTION_05MIN,
        RESOLUTION_15MIN,
        RESOLUTION_30MIN,
        RESOLUTION_45MIN,
        RESOLUTION_01HOUR,
        RESOLUTION_02HOUR,
        RESOLUTION_03HOUR,
        RESOLUTION_04HOUR,
        RESOLUTION_01DAY,
    ]
);

define(
    'RESOLUTION_ALIASES',
    [
        RESOLUTION_01MIN => '01m',
        RESOLUTION_03MIN => '03m',
        RESOLUTION_05MIN => '05m',
        RESOLUTION_15MIN => '15m',
        RESOLUTION_30MIN => '30m',
        RESOLUTION_45MIN => '45m',
        RESOLUTION_01HOUR => '01h',
        RESOLUTION_02HOUR => '02h',
        RESOLUTION_03HOUR => '03h',
        RESOLUTION_04HOUR => '04h',
        RESOLUTION_01DAY => '01d',
    ]
);

define('NORMAL_SCALE', 9);
define('EXTENDED_SCALE', 14);
