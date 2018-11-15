<?php
/**
 * User: alec
 * Date: 31.10.18
 * Time: 15:25
 */

// Trade constants
define('T_SELL', 'sell');
define('T_BUY', 'buy');

define('T_ASK', 'ask');
define('T_BID', 'bid');

define('RESOLUTION_01MIN', 60);
define('RESOLUTION_03MIN', 180);
define('RESOLUTION_05MIN', 300);
define('RESOLUTION_15MIN', 900);
define('RESOLUTION_30MIN', 1800);
define('RESOLUTION_45MIN', 2700);
define('RESOLUTION_01HOUR', 3600);
define('RESOLUTION_02HOUR', 7200);
define('RESOLUTION_03HOUR', 10800);
define('RESOLUTION_04HOUR', 14400);
define('RESOLUTION_01DAY', 86400);

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
